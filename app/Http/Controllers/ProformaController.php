<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\LogisticaMuestreo;
use App\Models\Parametro;
use App\Models\Proforma;
use App\Traits\RegistraMovimientoFinanciero;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProformaController extends Controller
{
    use RegistraMovimientoFinanciero;

    private $muestreadoPorOpciones = [
        'CLIENTE',
        'TÉCNICOS CIMA',
        'CONTRATISTA',
    ];

    /**
     * Verificar si el usuario tiene permisos de administración
     */
    private function esAdmin()
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        // Verificar por roles de Spatie (si existe el método)
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole(['admin', 'tecnico', 'analista']);
        }
        
        // Fallback por campo role o tipo
        return in_array($user->role ?? $user->tipo ?? 'usuario', ['admin', 'tecnico', 'analista']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Proforma::with('cliente');

        // ===== BÚSQUEDA POR TÉRMINO (CASE-INSENSITIVE) =====
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'ILIKE', "%{$search}%")
                    ->orWhere('tipo', 'ILIKE', "%{$search}%")
                    ->orWhere('tipo_muestra', 'ILIKE', "%{$search}%")
                    ->orWhereHas('cliente', function ($clientQuery) use ($search) {
                        $clientQuery->where('razon_social', 'ILIKE', "%{$search}%")
                            ->orWhere('persona_contacto', 'ILIKE', "%{$search}%");
                    });
            });
        }

        // Filtros por mes y año (basado en fecha_recepcion)
        if ($request->filled('mes') && $request->filled('anio')) {
            $query->whereMonth('fecha_recepcion', $request->mes)
                ->whereYear('fecha_recepcion', $request->anio);
        } elseif ($request->filled('mes')) {
            $query->whereMonth('fecha_recepcion', $request->mes)
                ->whereYear('fecha_recepcion', date('Y'));
        } elseif ($request->filled('anio')) {
            $query->whereYear('fecha_recepcion', $request->anio);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Obtener años disponibles para el filtro
        $añosDisponibles = Proforma::selectRaw('DISTINCT EXTRACT(YEAR FROM fecha_recepcion) as año')
            ->orderBy('año', 'desc')
            ->pluck('año')
            ->toArray();

        $proformas = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('proformas.index', compact('proformas', 'añosDisponibles'));
    }

    /**
     * Mostrar proformas eliminadas (papelera) - SOLO ADMIN
     */
    public function trash()
    {
        if (! $this->esAdmin()) {
            return redirect()->route('proformas.index')
                ->with('error', '⛔ Acceso denegado. Solo el administrador puede ver proformas eliminadas.');
        }

        $proformas = Proforma::onlyTrashed()
            ->with('cliente')
            ->latest('deleted_at')
            ->paginate(15);

        return view('proformas.trash', compact('proformas'));
    }

    /**
     * Restaurar proforma desde papelera - SOLO ADMIN
     */
    public function restore($id)
    {
        if (! $this->esAdmin()) {
            return redirect()->route('proformas.index')
                ->with('error', '⛔ Acceso denegado. Solo el administrador puede restaurar proformas.');
        }

        try {
            $proforma = Proforma::onlyTrashed()->findOrFail($id);
            $proforma->restore();

            return redirect()->route('proformas.trash')
                ->with('success', '✅ Proforma restaurada exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al restaurar proforma: '.$e->getMessage());

            return redirect()->route('proformas.trash')
                ->with('error', '❌ Error al restaurar la proforma: '.$e->getMessage());
        }
    }

    /**
     * Eliminar permanentemente de la base de datos - SOLO ADMIN
     */
    public function forceDelete($id)
    {
        if (! $this->esAdmin()) {
            return redirect()->route('proformas.index')
                ->with('error', '⛔ Acceso denegado. Solo el administrador puede eliminar permanentemente.');
        }

        try {
            $proforma = Proforma::onlyTrashed()->findOrFail($id);

            if ($proforma->informe) {
                return redirect()->route('proformas.trash')
                    ->with('error', '❌ No se puede eliminar permanentemente una proforma que tiene un informe asociado.');
            }

            $proforma->parametros()->detach();
            $proforma->forceDelete();

            return redirect()->route('proformas.trash')
                ->with('success', '✅ Proforma eliminada permanentemente.');

        } catch (\Exception $e) {
            Log::error('Error al eliminar permanentemente: '.$e->getMessage());

            return redirect()->route('proformas.trash')
                ->with('error', '❌ Error al eliminar permanentemente: '.$e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (! $this->esAdmin()) {
            return redirect()->route('proformas.index')
                ->with('error', '⛔ Solo el administrador puede crear proformas.');
        }

        $clientes = Cliente::orderBy('razon_social')->get();
        $parametros = Parametro::orderBy('nombre')->get();
        $logisticasMuestreo = LogisticaMuestreo::where('estado', true)->orderBy('categoria')->orderBy('costo')->get();
        $parametrosAmbientales = Parametro::where('tipo', 'AMBIENTAL')->orderBy('categoria')->orderBy('nombre')->get();

        return view('proformas.create', [
            'clientes' => $clientes,
            'parametros' => $parametros,
            'muestreadoPorOpciones' => $this->muestreadoPorOpciones,
            'logisticasMuestreo' => $logisticasMuestreo,
            'parametrosAmbientales' => $parametrosAmbientales,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (! $this->esAdmin()) {
            return redirect()->route('proformas.index')
                ->with('error', '⛔ Solo el administrador puede crear proformas.');
        }

        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo' => 'required|in:AMBIENTAL,ANALISIS_QUIMICO,INVESTIGACION',
            'tipo_muestra' => 'required|string|max:255',
            'unidad' => 'nullable|string|in:UIA,UAQ',
            'fecha_recepcion' => 'required|date',
            'fecha_muestreo' => 'required|date',
            'codigo_cliente' => 'nullable|array',
            'codigo_cliente.*' => 'nullable|string|max:100',
            'numero_recepcion' => 'nullable|string|max:50',
            'tipo_documento' => 'nullable|array',
            'tipo_documento.*' => 'string|in:PROFORMA,COTIZACION,CONTRATO,CONTRATO MODIFICADO',
            'persona_contacto' => 'nullable|string',
            'telefono_contacto' => 'nullable|string',
            'procedencia' => 'nullable|string',
            'zona_utm' => 'nullable|string|max:20',
            'punto_cardinal_1' => 'nullable|string|max:5',
            'valor_cardinal_1' => 'nullable|string|max:50',
            'punto_cardinal_2' => 'nullable|string|max:5',
            'valor_cardinal_2' => 'nullable|string|max:50',
            'muestreado_por' => 'nullable|string',
            'adelanto' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string',
            'parametros' => 'required|array|min:1',
            'parametros.*.id' => 'required|exists:parametros,id',
            'parametros.*.cantidad' => 'required|integer|min:1',
            'logisticas' => 'nullable|array',
            'logisticas.*.id' => 'required_with:logisticas|exists:logisticas_muestreo,id',
            'logisticas.*.cantidad' => 'required_with:logisticas|integer|min:1',
            'logisticas.*.descripcion' => 'nullable|string|max:500',
            'logisticas.*.precio_unitario' => 'nullable|numeric|min:0',
        ]);

        // ===== VALIDACIÓN ESTRICTA DE PARÁMETROS DUPLICADOS =====
        $parametroIds = collect($request->parametros)->pluck('id')->toArray();
        $parametrosUnicos = array_unique($parametroIds);

        if (count($parametroIds) !== count($parametrosUnicos)) {
            $duplicados = array_diff_assoc($parametroIds, $parametrosUnicos);
            $nombresDuplicados = [];

            foreach ($duplicados as $id) {
                $parametro = Parametro::find($id);
                if ($parametro) {
                    $nombresDuplicados[] = $parametro->nombre;
                }
            }

            $mensaje = '❌ No se permiten parámetros duplicados. ';
            if (! empty($nombresDuplicados)) {
                $mensaje .= 'Parámetros repetidos: '.implode(', ', array_unique($nombresDuplicados));
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $mensaje);
        }

        try {
            DB::beginTransaction();

            // ===== GENERAR CÓDIGO CON NUEVO FORMATO =====
            $codigo = Proforma::generarCodigo($request->unidad, $request->tipo);

            $proforma = Proforma::create([
                'codigo' => $codigo,
                'codigo_cliente' => $request->codigo_cliente,
                'cliente_id' => $request->cliente_id,
                'tipo' => $request->tipo,
                'tipo_documento' => $request->tipo_documento,
                'tipo_muestra' => $request->tipo_muestra,
                'numero_recepcion' => $request->numero_recepcion,
                'unidad' => $request->unidad,
                'fecha_recepcion' => $request->fecha_recepcion,
                'fecha_muestreo' => $request->fecha_muestreo,
                'hora_recepcion' => now()->format('H:i'),
                'persona_contacto' => $request->persona_contacto,
                'telefono_contacto' => $request->telefono_contacto,
                'procedencia' => $request->procedencia,
                'zona_utm' => $request->zona_utm,
                'punto_cardinal_1' => $request->punto_cardinal_1,
                'valor_cardinal_1' => $request->valor_cardinal_1,
                'punto_cardinal_2' => $request->punto_cardinal_2,
                'valor_cardinal_2' => $request->valor_cardinal_2,
                'muestreado_por' => $request->muestreado_por,
                'adelanto' => $request->adelanto ?? 0,
                'observaciones' => $request->observaciones,
                'estado' => 'BORRADOR',
                'parametros_modificados' => false,
                'subtotal' => 0,
                'descuento' => 0,
                'total' => 0,
                'saldo' => 0,
            ]);

            // Adjuntar parámetros
            foreach ($request->parametros as $parametroData) {
                if (isset($parametroData['id']) && isset($parametroData['cantidad'])) {
                    $parametro = Parametro::find($parametroData['id']);
                    $pivotData = [
                        'cantidad_muestras' => $parametroData['cantidad'],
                        'precio_unitario' => $parametro->precio_unitario,
                    ];
                    if (isset($parametroData['metodo'])) {
                        $pivotData['metodo'] = $parametroData['metodo'];
                    }
                    $proforma->parametros()->attach($parametroData['id'], $pivotData);
                }
            }

            // Guardar logística de muestreo con precio editable
            $totalLogistica = 0;
            if ($request->has('logisticas')) {
                foreach ($request->logisticas as $logData) {
                    if (isset($logData['id']) && isset($logData['cantidad'])) {
                        $logistica = LogisticaMuestreo::find($logData['id']);
                        $precioUnitario = $logData['precio_unitario'] ?? $logistica->costo ?? 0;
                        $subtotal = $precioUnitario * $logData['cantidad'];

                        $proforma->logisticasMuestreo()->attach($logData['id'], [
                            'cantidad' => $logData['cantidad'],
                            'subtotal' => $subtotal,
                            'descripcion' => $logData['descripcion'] ?? null,
                            'precio_unitario' => $precioUnitario,
                        ]);
                        $totalLogistica += $subtotal;
                    }
                }
            }

            // Calcular totales
            $proforma->load('parametros');

            $subtotal = 0;
            foreach ($proforma->parametros as $parametro) {
                $subtotal += $parametro->pivot->cantidad_muestras * $parametro->pivot->precio_unitario;
            }

            $descuento = ($proforma->tipo === 'INVESTIGACION') ? $subtotal * 0.20 : 0;
            $total = $subtotal + $totalLogistica - $descuento;
            $saldo = $total - $proforma->adelanto;

            $proforma->subtotal = $subtotal + $totalLogistica;
            $proforma->descuento = $descuento;
            $proforma->total = $total;
            $proforma->saldo = $saldo;
            $proforma->save();

            // Registrar movimiento financiero
            $this->registrarMovimiento(
                $proforma,
                $proforma->cliente_id,
                'DEUDA',
                $proforma->total,
                "Creación de proforma {$proforma->codigo}",
                $proforma->codigo,
                "Proforma creada con total Bs. {$proforma->total}"
            );

            DB::commit();

            return redirect()->route('proformas.show', $proforma)
                ->with('success', "✅ Proforma creada exitosamente. Código: {$proforma->codigo}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear proforma: '.$e->getMessage());

            return back()->withInput()
                ->with('error', '❌ Error al crear proforma: '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Proforma $proforma)
    {
        $proforma->load(['cliente', 'parametros', 'informe', 'usuarioModificacion', 'logisticasMuestreo']);

        return view('proformas.show', compact('proforma'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proforma $proforma)
    {
        if (! $this->esAdmin()) {
            return redirect()->route('proformas.show', $proforma)
                ->with('error', '⛔ Solo el administrador puede editar proformas.');
        }

        if ($proforma->estado !== 'BORRADOR') {
            return redirect()->route('proformas.show', $proforma)
                ->with('error', "❌ No se puede editar una proforma en estado {$proforma->estado}");
        }

        $proforma->load('logisticasMuestreo');

        $clientes = Cliente::orderBy('razon_social')->get();
        $parametros = Parametro::orderBy('nombre')->get();
        $logisticasMuestreo = LogisticaMuestreo::where('estado', true)->orderBy('categoria')->orderBy('costo')->get();
        $parametrosAmbientales = Parametro::where('tipo', 'AMBIENTAL')->orderBy('categoria')->orderBy('nombre')->get();

        return view('proformas.edit', [
            'proforma' => $proforma,
            'clientes' => $clientes,
            'parametros' => $parametros,
            'muestreadoPorOpciones' => $this->muestreadoPorOpciones,
            'logisticasMuestreo' => $logisticasMuestreo,
            'parametrosAmbientales' => $parametrosAmbientales,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proforma $proforma)
    {
        if (! $this->esAdmin()) {
            return redirect()->route('proformas.show', $proforma)
                ->with('error', '⛔ Solo el administrador puede actualizar proformas.');
        }

        if ($proforma->estado !== 'BORRADOR') {
            return redirect()->route('proformas.show', $proforma)
                ->with('error', "❌ No se puede actualizar una proforma en estado {$proforma->estado}");
        }

        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo' => 'required|in:AMBIENTAL,ANALISIS_QUIMICO,INVESTIGACION',
            'tipo_muestra' => 'required|string|max:255',
            'unidad' => 'nullable|string|in:UIA,UAQ',
            'fecha_recepcion' => 'required|date',
            'fecha_muestreo' => 'required|date',
            'codigo_cliente' => 'nullable|array',
            'codigo_cliente.*' => 'nullable|string|max:100',
            'numero_recepcion' => 'nullable|string|max:50',
            'tipo_documento' => 'nullable|array',
            'tipo_documento.*' => 'string|in:PROFORMA,COTIZACION,CONTRATO,CONTRATO MODIFICADO',
            'persona_contacto' => 'nullable|string',
            'telefono_contacto' => 'nullable|string',
            'procedencia' => 'nullable|string',
            'zona_utm' => 'nullable|string|max:20',
            'punto_cardinal_1' => 'nullable|string|max:5',
            'valor_cardinal_1' => 'nullable|string|max:50',
            'punto_cardinal_2' => 'nullable|string|max:5',
            'valor_cardinal_2' => 'nullable|string|max:50',
            'muestreado_por' => 'nullable|string',
            'adelanto' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string',
            'parametros' => 'required|array|min:1',
            'parametros.*.id' => 'required|exists:parametros,id',
            'parametros.*.cantidad' => 'required|integer|min:1',
            'logisticas' => 'nullable|array',
            'logisticas.*.id' => 'required_with:logisticas|exists:logisticas_muestreo,id',
            'logisticas.*.cantidad' => 'required_with:logisticas|integer|min:1',
            'logisticas.*.descripcion' => 'nullable|string|max:500',
            'logisticas.*.precio_unitario' => 'nullable|numeric|min:0',
            'justificacion_modificacion' => 'nullable|string',
        ]);

        // ===== VALIDACIÓN DUPLICADOS =====
        $parametroIds = collect($request->parametros)->pluck('id')->toArray();
        $parametrosUnicos = array_unique($parametroIds);

        if (count($parametroIds) !== count($parametrosUnicos)) {
            $duplicados = array_diff_assoc($parametroIds, $parametrosUnicos);
            $nombresDuplicados = [];

            foreach ($duplicados as $id) {
                $parametro = Parametro::find($id);
                if ($parametro) {
                    $nombresDuplicados[] = $parametro->nombre;
                }
            }

            $mensaje = '❌ No se permiten parámetros duplicados. ';
            if (! empty($nombresDuplicados)) {
                $mensaje .= 'Parámetros repetidos: '.implode(', ', array_unique($nombresDuplicados));
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $mensaje);
        }

        try {
            DB::beginTransaction();

            $parametrosActuales = $proforma->parametros()->pluck('parametros.id')->toArray();
            $parametrosNuevos = collect($request->parametros)->pluck('id')->toArray();
            $parametrosAgregados = array_diff($parametrosNuevos, $parametrosActuales);
            $parametrosEliminados = array_diff($parametrosActuales, $parametrosNuevos);
            $parametrosModificados = ! empty($parametrosAgregados) || ! empty($parametrosEliminados);

            $proforma->update([
                'cliente_id' => $request->cliente_id,
                'tipo' => $request->tipo,
                'tipo_muestra' => $request->tipo_muestra,
                'unidad' => $request->unidad,
                'fecha_recepcion' => $request->fecha_recepcion,
                'fecha_muestreo' => $request->fecha_muestreo,
                'codigo_cliente' => $request->codigo_cliente,
                'numero_recepcion' => $request->numero_recepcion,
                'hora_recepcion' => now()->format('H:i'),
                'tipo_documento' => $request->tipo_documento,
                'persona_contacto' => $request->persona_contacto,
                'telefono_contacto' => $request->telefono_contacto,
                'procedencia' => $request->procedencia,
                'zona_utm' => $request->zona_utm,
                'punto_cardinal_1' => $request->punto_cardinal_1,
                'valor_cardinal_1' => $request->valor_cardinal_1,
                'punto_cardinal_2' => $request->punto_cardinal_2,
                'valor_cardinal_2' => $request->valor_cardinal_2,
                'muestreado_por' => $request->muestreado_por,
                'adelanto' => $request->adelanto ?? 0,
                'observaciones' => $request->observaciones,
            ]);

            if ($parametrosModificados) {
                if (empty($request->justificacion_modificacion)) {
                    throw new \Exception('Debe proporcionar una justificación para modificar los parámetros.');
                }

                $proforma->parametros_modificados = true;
                $proforma->justificacion_modificacion = $request->justificacion_modificacion;
                $proforma->modificado_por = Auth::id();
                $proforma->save();
            }

            $parametrosSync = [];
            foreach ($request->parametros as $parametroData) {
                if (isset($parametroData['id']) && isset($parametroData['cantidad'])) {
                    $parametro = Parametro::find($parametroData['id']);
                    $pivotData = [
                        'cantidad_muestras' => $parametroData['cantidad'],
                        'precio_unitario' => $parametro->precio_unitario,
                    ];
                    if (isset($parametroData['metodo'])) {
                        $pivotData['metodo'] = $parametroData['metodo'];
                    }
                    $parametrosSync[$parametroData['id']] = $pivotData;
                }
            }

            $proforma->parametros()->sync($parametrosSync);
            $proforma->load('parametros');

            $totalLogistica = 0;
            if ($request->has('logisticas')) {
                $logisticasSync = [];
                foreach ($request->logisticas as $logData) {
                    if (isset($logData['id']) && isset($logData['cantidad'])) {
                        $logistica = LogisticaMuestreo::find($logData['id']);
                        $precioUnitario = $logData['precio_unitario'] ?? $logistica->costo ?? 0;
                        $subtotal = $precioUnitario * $logData['cantidad'];

                        $logisticasSync[$logData['id']] = [
                            'cantidad' => $logData['cantidad'],
                            'subtotal' => $subtotal,
                            'descripcion' => $logData['descripcion'] ?? null,
                            'precio_unitario' => $precioUnitario,
                        ];
                        $totalLogistica += $subtotal;
                    }
                }
                $proforma->logisticasMuestreo()->sync($logisticasSync);
            } else {
                $proforma->logisticasMuestreo()->detach();
            }

            $subtotal = 0;
            foreach ($proforma->parametros as $parametro) {
                $subtotal += $parametro->pivot->cantidad_muestras * $parametro->pivot->precio_unitario;
            }

            $descuento = ($proforma->tipo === 'INVESTIGACION') ? $subtotal * 0.20 : 0;
            $total = $subtotal + $totalLogistica - $descuento;
            $saldo = $total - $proforma->adelanto;

            $proforma->subtotal = $subtotal + $totalLogistica;
            $proforma->descuento = $descuento;
            $proforma->total = $total;
            $proforma->saldo = $saldo;
            $proforma->save();

            DB::commit();

            if ($parametrosModificados) {
                return redirect()->route('proformas.show', $proforma)
                    ->with('success', '✅ Proforma actualizada exitosamente. Se ha registrado la modificación de parámetros.');
            }

            return redirect()->route('proformas.show', $proforma)
                ->with('success', '✅ Proforma actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar proforma: '.$e->getMessage());

            return back()->withInput()
                ->with('error', '❌ Error al actualizar proforma: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proforma $proforma)
    {
        if (! $this->esAdmin()) {
            return redirect()->route('proformas.index')
                ->with('error', '⛔ Solo el administrador puede eliminar proformas.');
        }

        if ($proforma->estado !== 'BORRADOR') {
            return redirect()->route('proformas.show', $proforma)
                ->with('error', "❌ No se puede eliminar una proforma en estado {$proforma->estado}");
        }

        if ($proforma->informe && !$this->esAdmin()) {
            return redirect()->route('proformas.show', $proforma)
                ->with('error', '❌ Solo el administrador puede eliminar proformas con informes asociados.');
        }

        try {
            DB::beginTransaction();

            $proforma->delete();

            DB::commit();

            return redirect()->route('proformas.index')
                ->with('success', '✅ Proforma movida a la papelera exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar proforma: '.$e->getMessage());

            return redirect()->route('proformas.index')
                ->with('error', '❌ Error al eliminar proforma: '.$e->getMessage());
        }
    }

    /**
     * Cambiar estado de la proforma
     */
    public function cambiarEstado(Request $request, Proforma $proforma)
    {
        if (! $this->esAdmin()) {
            return back()->with('error', '⛔ Solo el administrador puede cambiar el estado de proformas.');
        }

        $request->validate([
            'estado' => 'required|in:BORRADOR,ENVIADA,APROBADA,RECHAZADA,FINALIZADA',
        ]);

        try {
            $estadoAnterior = $proforma->estado;
            $nuevoEstado = $request->estado;

            $transicionesPermitidas = [
                'BORRADOR' => ['ENVIADA', 'RECHAZADA'],
                'ENVIADA' => ['APROBADA', 'RECHAZADA'],
                'APROBADA' => ['FINALIZADA', 'RECHAZADA'],
                'RECHAZADA' => ['BORRADOR'],
                'FINALIZADA' => [],
            ];

            if (! in_array($nuevoEstado, $transicionesPermitidas[$estadoAnterior] ?? [])) {
                return back()->with('error', '❌ Transición de estado no permitida.');
            }

            $proforma->update(['estado' => $nuevoEstado]);

            return redirect()->route('proformas.show', $proforma)
                ->with('success', "✅ Estado cambiado a {$nuevoEstado}");

        } catch (\Exception $e) {
            Log::error('Error al cambiar estado: '.$e->getMessage());

            return back()->with('error', '❌ Error al cambiar estado');
        }
    }

    /**
     * Convierte un número entero (0 a 999,999,999) a su representación
     * en letras, en español, en mayúsculas.
     */
    private function numeroALetras(int $numero): string
    {
        if ($numero === 0) {
            return 'CERO';
        }

        if ($numero < 0) {
            return 'MENOS '.$this->numeroALetras(abs($numero));
        }

        $unidades = [
            '', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO',
            'SEIS', 'SIETE', 'OCHO', 'NUEVE',
        ];

        $especiales10a19 = [
            10 => 'DIEZ', 11 => 'ONCE', 12 => 'DOCE', 13 => 'TRECE', 14 => 'CATORCE',
            15 => 'QUINCE', 16 => 'DIECISÉIS', 17 => 'DIECISIETE', 18 => 'DIECIOCHO', 19 => 'DIECINUEVE',
        ];

        $especiales20a29 = [
            20 => 'VEINTE', 21 => 'VEINTIUN', 22 => 'VEINTIDÓS', 23 => 'VEINTITRÉS',
            24 => 'VEINTICUATRO', 25 => 'VEINTICINCO', 26 => 'VEINTISÉIS',
            27 => 'VEINTISIETE', 28 => 'VEINTIOCHO', 29 => 'VEINTINUEVE',
        ];

        $decenas = [
            30 => 'TREINTA', 40 => 'CUARENTA', 50 => 'CINCUENTA',
            60 => 'SESENTA', 70 => 'SETENTA', 80 => 'OCHENTA', 90 => 'NOVENTA',
        ];

        $centenas = [
            200 => 'DOSCIENTOS', 300 => 'TRESCIENTOS', 400 => 'CUATROCIENTOS',
            500 => 'QUINIENTOS', 600 => 'SEISCIENTOS', 700 => 'SETECIENTOS',
            800 => 'OCHOCIENTOS', 900 => 'NOVECIENTOS',
        ];

        $convertirGrupo = function (int $n) use ($unidades, $especiales10a19, $especiales20a29, $decenas, $centenas) {
            if ($n === 0) {
                return '';
            }

            $texto = '';
            $c = intdiv($n, 100) * 100;
            $resto = $n % 100;

            if ($c > 0) {
                if ($c === 100) {
                    $texto .= ($resto === 0) ? 'CIEN' : 'CIENTO';
                } else {
                    $texto .= $centenas[$c];
                }
            }

            if ($resto > 0) {
                if ($texto !== '') {
                    $texto .= ' ';
                }

                if ($resto < 10) {
                    $texto .= $unidades[$resto];
                } elseif ($resto < 20) {
                    $texto .= $especiales10a19[$resto];
                } elseif ($resto < 30) {
                    $texto .= $especiales20a29[$resto];
                } else {
                    $d = intdiv($resto, 10) * 10;
                    $u = $resto % 10;
                    $texto .= $decenas[$d];
                    if ($u > 0) {
                        $texto .= ' Y '.$unidades[$u];
                    }
                }
            }

            return $texto;
        };

        $millones = intdiv($numero, 1000000);
        $resto1 = $numero % 1000000;
        $miles = intdiv($resto1, 1000);
        $resto2 = $resto1 % 1000;

        $partes = [];

        if ($millones > 0) {
            $partes[] = ($millones === 1)
                ? 'UN MILLÓN'
                : $convertirGrupo($millones).' MILLONES';
        }

        if ($miles > 0) {
            $partes[] = ($miles === 1)
                ? 'MIL'
                : $convertirGrupo($miles).' MIL';
        }

        if ($resto2 > 0) {
            $partes[] = $convertirGrupo($resto2);
        }

        return trim(implode(' ', $partes));
    }

    /**
     * Genera PDF de la proforma - DESCARGA DIRECTA
     */
    public function pdf(Proforma $proforma)
    {
        try {
            $proforma->load(['cliente', 'parametros', 'usuarioModificacion', 'logisticasMuestreo']);

            $entero = intval($proforma->total);
            $decimal = round(($proforma->total - $entero) * 100);

            $totalEnLetras = 'SON: '.$this->numeroALetras($entero).' '.str_pad($decimal, 2, '0', STR_PAD_LEFT).'/100 BOLIVIANOS';

            $cfg = \App\Models\Documento::whereSlug($proforma->tipo === 'AMBIENTAL' ? 'solicitud-ensayo-ambiental' : 'solicitud-ensayo')->first() ?? new \App\Models\Documento;

            $data = [
                'proforma' => $proforma,
                'totalEnLetras' => $totalEnLetras,
                'cfg' => $cfg,
            ];

            $pdfConteo = Pdf::loadView('proformas.pdf', $data);
            $pdfConteo->setPaper('a4', 'portrait');
            $pdfConteo->render();
            $totalPaginas = $pdfConteo->getDomPDF()->getCanvas()->get_page_count();

            $pdf = Pdf::loadView('proformas.pdf', array_merge($data, [
                'totalPaginas' => $totalPaginas,
            ]));
            $pdf->setPaper('a4', 'portrait');

            return $pdf->download("proforma-{$proforma->codigo}.pdf");

        } catch (\Exception $e) {
            Log::error('Error al generar PDF: '.$e->getMessage());
            return back()->with('error', '❌ Error al generar PDF');
        }
    }

    /**
     * GENERAR PDF - CADENA DE CUSTODIA (DESCARGA DIRECTA)
     */
    public function pdfCadenaCustodia(Proforma $proforma)
    {
        try {
            $proforma->load(['cliente', 'parametros', 'usuarioModificacion']);

            $data = [
                'proforma' => $proforma,
                'observacion' => $proforma->observaciones ?? '',
            ];

            $pdf = Pdf::loadView('proformas.cadena_custodia', $data);
            $pdf->setPaper('a4', 'landscape');

            return $pdf->download("cadena-de-custodia-{$proforma->codigo}.pdf");

        } catch (\Exception $e) {
            Log::error('Error al generar Cadena de Custodia PDF: '.$e->getMessage());
            return back()->with('error', '❌ Error al generar Cadena de Custodia: '.$e->getMessage());
        }
    }

    /**
     * Actualizar solo el adelanto de la proforma
     */
    public function actualizarAdelanto(Request $request, Proforma $proforma)
    {
        if (! $this->esAdmin()) {
            return redirect()->route('proformas.show', $proforma)
                ->with('error', '⛔ Acceso denegado. Solo el administrador puede actualizar proformas.');
        }

        if (! in_array($proforma->estado, ['ENVIADA', 'APROBADA'])) {
            return redirect()->route('proformas.show', $proforma)
                ->with('error', "❌ Solo se puede actualizar el adelanto en proformas ENVIADA o APROBADA. Estado actual: {$proforma->estado}");
        }

        $request->validate([
            'adelanto' => 'required|numeric|min:0|max:'.$proforma->total,
        ]);

        try {
            DB::beginTransaction();

            $adelantoAnterior = $proforma->adelanto;
            $nuevoAdelanto = $request->adelanto;

            $montoPagado = $nuevoAdelanto - $adelantoAnterior;

            $proforma->adelanto = $nuevoAdelanto;
            $proforma->saldo = $proforma->total - $proforma->adelanto;
            $proforma->save();

            if ($montoPagado > 0) {
                $this->registrarMovimiento(
                    $proforma,
                    $proforma->cliente_id,
                    'PAGO',
                    $montoPagado,
                    "Pago de adelanto para proforma {$proforma->codigo}",
                    $proforma->codigo,
                    "Nuevo adelanto: Bs. {$nuevoAdelanto} (anterior: Bs. {$adelantoAnterior})"
                );
            } elseif ($montoPagado < 0) {
                $this->registrarMovimiento(
                    $proforma,
                    $proforma->cliente_id,
                    'AJUSTE',
                    $montoPagado,
                    "Reducción de adelanto para proforma {$proforma->codigo}",
                    $proforma->codigo,
                    "Nuevo adelanto: Bs. {$nuevoAdelanto} (anterior: Bs. {$adelantoAnterior})"
                );
            }

            DB::commit();

            return redirect()->route('proformas.show', $proforma)
                ->with('success', '✅ Adelanto actualizado exitosamente. Nuevo saldo: Bs. '.number_format($proforma->saldo, 2));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar adelanto: '.$e->getMessage());

            return redirect()->route('proformas.show', $proforma)
                ->with('error', '❌ Error al actualizar adelanto: '.$e->getMessage());
        }
    }
}