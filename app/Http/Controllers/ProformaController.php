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

        // Filtros por mes y año
        if ($request->filled('mes') && $request->filled('anio')) {
            $query->whereMonth('fecha_emision', $request->mes)
                ->whereYear('fecha_emision', $request->anio);
        } elseif ($request->filled('mes')) {
            $query->whereMonth('fecha_emision', $request->mes)
                ->whereYear('fecha_emision', date('Y'));
        } elseif ($request->filled('anio')) {
            $query->whereYear('fecha_emision', $request->anio);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $añosDisponibles = Proforma::selectRaw('DISTINCT EXTRACT(YEAR FROM fecha_emision) as año')
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
            'tipo' => 'required|in:AMBIENTAL,ANALISIS QUIMICO,INVESTIGACION',
            'tipo_muestra' => 'required|string|max:255',
            'unidad' => 'nullable|string|in:UIA,UAQ',
            'fecha_emision' => 'required|date',
            'fecha_recepcion' => 'required|date',
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

            // ===== GENERAR CÓDIGO =====
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
                'fecha_emision' => $request->fecha_emision,
                'fecha_recepcion' => $request->fecha_recepcion,
                'fecha_inicio_ensayo' => $request->fecha_recepcion,
                'fecha_conclusion_ensayo' => now()->addDays(15)->format('Y-m-d'),
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

            // Logística
            $totalLogistica = 0;
            if ($request->has('logisticas') && $request->tipo === 'AMBIENTAL') {
                foreach ($request->logisticas as $logData) {
                    if (isset($logData['id']) && isset($logData['cantidad'])) {
                        $logistica = LogisticaMuestreo::find($logData['id']);
                        $proforma->logisticasMuestreo()->attach($logData['id'], [
                            'cantidad' => $logData['cantidad'],
                            'subtotal' => $logistica->costo,
                            'descripcion' => $logData['descripcion'] ?? null,
                        ]);
                        $totalLogistica += $logistica->costo;
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
            'tipo' => 'required|in:AMBIENTAL,ANALISIS QUIMICO,INVESTIGACION',
            'tipo_muestra' => 'required|string|max:255',
            'unidad' => 'nullable|string|in:UIA,UAQ',
            'fecha_emision' => 'required|date',
            'fecha_recepcion' => 'required|date',
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
                'fecha_emision' => $request->fecha_emision,
                'fecha_recepcion' => $request->fecha_recepcion,
                'fecha_inicio_ensayo' => $request->fecha_recepcion,
                'fecha_conclusion_ensayo' => now()->addDays(15)->format('Y-m-d'),
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
            if ($request->has('logisticas') && $request->tipo === 'AMBIENTAL') {
                $logisticasSync = [];
                foreach ($request->logisticas as $logData) {
                    if (isset($logData['id']) && isset($logData['cantidad'])) {
                        $logistica = LogisticaMuestreo::find($logData['id']);
                        $logisticasSync[$logData['id']] = [
                            'cantidad' => $logData['cantidad'],
                            'subtotal' => $logistica->costo,
                            'descripcion' => $logData['descripcion'] ?? null,
                        ];
                        $totalLogistica += $logistica->costo;
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
     * Genera PDF de la proforma
     *
     * NOTA: El pie de página (dirección, teléfono y numeración) ya NO se dibuja
     * aquí con page_script/canvas. Se maneja íntegramente por CSS dentro de la
     * vista "proformas.pdf" usando "position: fixed" y los contadores
     * counter(page)/counter(pages), que DomPDF soporta de forma nativa y
     * repite automáticamente en cada página, sin depender del orden de
     * ejecución de loadView/setPaper/getCanvas.
     */
    public function pdf(Proforma $proforma)
    {
        try {
            $proforma->load(['cliente', 'parametros', 'usuarioModificacion', 'logisticasMuestreo']);

            $entero = intval($proforma->total);
            $decimal = round(($proforma->total - $entero) * 100);

            $mapaNumeros = [
                0 => 'CERO', 1 => 'UN', 2 => 'DOS', 3 => 'TRES', 4 => 'CUATRO',
                5 => 'CINCO', 6 => 'SEIS', 7 => 'SIETE', 8 => 'OCHO', 9 => 'NUEVE',
                10 => 'DIEZ', 11 => 'ONCE', 12 => 'DOCE', 13 => 'TRECE', 14 => 'CATORCE',
                15 => 'QUINCE', 16 => 'DIECISÉIS', 17 => 'DIECISIETE', 18 => 'DIECIOCHO',
                19 => 'DIECINUEVE', 20 => 'VEINTE', 21 => 'VEINTIUN', 22 => 'VEINTIDOS',
                23 => 'VEINTITRES', 24 => 'VEINTICUATRO', 25 => 'VEINTICINCO',
                26 => 'VEINTISÉIS', 27 => 'VEINTISIETE', 28 => 'VEINTIOCHO', 29 => 'VEINTINUEVE',
                30 => 'TREINTA', 40 => 'CUARENTA', 50 => 'CINCUENTA', 60 => 'SESENTA',
                70 => 'SETENTA', 80 => 'OCHENTA', 90 => 'NOVENTA',
                100 => 'CIEN', 200 => 'DOSCIENTOS', 300 => 'TRESCIENTOS', 400 => 'CUATROCIENTOS',
                500 => 'QUINIENTOS', 600 => 'SEISCIENTOS', 700 => 'SETECIENTOS', 800 => 'OCHOCIENTOS',
                900 => 'NOVECIENTOS',
            ];

            $numeroEnLetras = function ($numero) use (&$numeroEnLetras, $mapaNumeros) {
                if ($numero <= 29) {
                    return $mapaNumeros[$numero];
                } elseif ($numero < 100) {
                    $decena = (int) floor($numero / 10) * 10;
                    $unidad = $numero % 10;
                    if ($unidad == 0) {
                        return $mapaNumeros[$decena];
                    } else {
                        return $mapaNumeros[$decena].' Y '.$mapaNumeros[$unidad];
                    }
                } elseif ($numero < 1000) {
                    $centena = (int) floor($numero / 100) * 100;
                    $resto = $numero % 100;
                    if ($resto == 0) {
                        return $mapaNumeros[$centena];
                    } else {
                        return $mapaNumeros[$centena].' '.$numeroEnLetras($resto);
                    }
                } elseif ($numero < 1000000) {
                    $miles = (int) floor($numero / 1000);
                    $resto = $numero % 1000;
                    if ($resto == 0) {
                        return $numeroEnLetras($miles).' MIL';
                    } else {
                        return $numeroEnLetras($miles).' MIL '.$numeroEnLetras($resto);
                    }
                }

                return number_format($numero, 0);
            };

            $letras = $numeroEnLetras($entero);
            $totalEnLetras = 'SON: '.strtoupper($letras).' '.str_pad($decimal, 2, '0', STR_PAD_LEFT).'/100 BOLIVIANOS';

            $cfg = \App\Models\Documento::whereSlug($proforma->tipo === 'AMBIENTAL' ? 'solicitud-ensayo-ambiental' : 'solicitud-ensayo')->first() ?? new \App\Models\Documento;

            $data = [
                'proforma' => $proforma,
                'totalEnLetras' => $totalEnLetras,
                'cfg' => $cfg,
            ];

            // ===== NUMERACIÓN "Página X de Y" (enfoque de doble renderizado) =====
            // Tanto counter(pages) en CSS, como $canvas->page_script(), como
            // $canvas->page_text() con tokens {PAGE_NUM}/{PAGE_COUNT}
            // demostraron ser poco confiables en este entorno (total
            // incorrecto y/o no se repiten en todas las páginas).
            //
            // Este enfoque es determinístico y no depende de ningún
            // mecanismo especial de DomPDF para el TOTAL:
            //   1) Se renderiza el documento una vez, sin mostrarlo, solo
            //      para contar cuántas páginas tiene realmente.
            //   2) Se renderiza una segunda vez pasándole ese total ya
            //      conocido como un dato normal de la vista ($totalPaginas).
            // El número de página ACTUAL sí se obtiene con CSS
            // "counter(page)", que en este entorno funciona correctamente
            // (fue "counter(pages)", el total, el que fallaba).
            $pdfConteo = Pdf::loadView('proformas.pdf', $data + ['totalPaginas' => 1]);
            $pdfConteo->setPaper('A4', 'portrait');
            $dompdfConteo = $pdfConteo->getDomPDF();
            $dompdfConteo->render();
            $totalPaginas = $dompdfConteo->getCanvas()->get_page_count();

            $pdf = Pdf::loadView('proformas.pdf', $data + ['totalPaginas' => $totalPaginas]);
            $pdf->setPaper('A4', 'portrait');

            return $pdf->stream("proforma-{$proforma->codigo}.pdf");

        } catch (\Exception $e) {
            Log::error('Error al generar PDF: '.$e->getMessage());

            return back()->with('error', '❌ Error al generar PDF: '.$e->getMessage());
        }
    }

    /**
     * GENERAR PDF2- CADENA DE CUSTODIA
     *
     * NOTA: igual que en pdf(), el pie de página se maneja por CSS
     * (position: fixed + counter(page)/counter(pages)) dentro de la vista
     * "proformas.cadena_custodia". Si aún no lo tiene, agrega el mismo
     * bloque .pie-pagina que se usó en "proformas.pdf".
     */
    public function pdfCadenaCustodia(Proforma $proforma)
    {
        try {
            $proforma->load(['cliente', 'parametros', 'usuarioModificacion']);

            $entero = intval($proforma->total);
            $decimal = round(($proforma->total - $entero) * 100);

            $mapaNumeros = [
                0 => 'CERO', 1 => 'UN', 2 => 'DOS', 3 => 'TRES', 4 => 'CUATRO',
                5 => 'CINCO', 6 => 'SEIS', 7 => 'SIETE', 8 => 'OCHO', 9 => 'NUEVE',
                10 => 'DIEZ', 11 => 'ONCE', 12 => 'DOCE', 13 => 'TRECE', 14 => 'CATORCE',
                15 => 'QUINCE', 16 => 'DIECISÉIS', 17 => 'DIECISIETE', 18 => 'DIECIOCHO',
                19 => 'DIECINUEVE', 20 => 'VEINTE', 30 => 'TREINTA', 40 => 'CUARENTA',
                50 => 'CINCUENTA', 60 => 'SESENTA', 70 => 'SETENTA', 80 => 'OCHENTA',
                90 => 'NOVENTA', 100 => 'CIEN', 200 => 'DOSCIENTOS', 300 => 'TRESCIENTOS',
                400 => 'CUATROCIENTOS', 500 => 'QUINIENTOS', 600 => 'SEISCIENTOS',
                700 => 'SETECIENTOS', 800 => 'OCHOCIENTOS', 900 => 'NOVECIENTOS',
            ];

            $numeroEnLetras = function ($numero) use (&$numeroEnLetras, $mapaNumeros) {
                if ($numero <= 20) {
                    return $mapaNumeros[$numero];
                } elseif ($numero < 100) {
                    $decena = (int) floor($numero / 10) * 10;
                    $unidad = $numero % 10;
                    if ($unidad == 0) {
                        return $mapaNumeros[$decena];
                    } else {
                        return $mapaNumeros[$decena].' Y '.$mapaNumeros[$unidad];
                    }
                } elseif ($numero < 1000) {
                    $centena = (int) floor($numero / 100) * 100;
                    $resto = $numero % 100;
                    if ($resto == 0) {
                        return $mapaNumeros[$centena];
                    } else {
                        return $mapaNumeros[$centena].' '.$numeroEnLetras($resto);
                    }
                }

                return number_format($numero, 0);
            };

            $letras = $numeroEnLetras($entero);
            $totalEnLetras = 'SON: '.strtoupper($letras).' '.str_pad($decimal, 2, '0', STR_PAD_LEFT).'/100 BOLIVIANOS';

            $parametrosAgrupados = $this->agruparParametrosCadena($proforma->parametros ?? collect());

            $muestraData = (object) [
                'tipo_muestra' => $proforma->tipo_muestra ?? 'No especificado',
                'identificacion' => $proforma->codigo ?? 'M-001',
                'codigo' => $proforma->codigo,
                'codigo_lab' => 'LAB-'.str_pad($proforma->id ?? '1', 4, '0', STR_PAD_LEFT),
                'campo_id' => $proforma->procedencia ?? 'Campo',
                'fecha_muestreo' => $proforma->fecha_recepcion,
                'fecha_recepcion' => $proforma->fecha_emision,
                'hora_muestreo' => null,
                'punto_muestreo' => $proforma->coordenadas ?? 'No especificado',
                'muestreado_por' => $proforma->muestreado_por ?? 'No especificado',
                'observaciones' => $proforma->observaciones,
                'procedencia' => $proforma->procedencia,
                'coordenadas' => $proforma->coordenadas,
                'persona_contacto' => $proforma->persona_contacto,
                'telefono_contacto' => $proforma->telefono_contacto,
            ];

            $data = [
                'proforma' => $proforma,
                'totalEnLetras' => $totalEnLetras,
                'fechaActual' => now()->format('d/m/Y H:i'),
                'numeroContrato' => $proforma->codigo ?? 'S/N',
                'fechaContrato' => $proforma->fecha_emision?->format('Y-m-d') ?? now()->format('Y-m-d'),
                'fechaRecepcion' => $proforma->fecha_recepcion?->format('Y-m-d') ?? 'No registrada',
                'parametrosAgrupados' => $parametrosAgrupados,
                'muestra' => $muestraData,
                'muestreadoPorOpciones' => $this->muestreadoPorOpciones,
            ];

            // ===== NUMERACIÓN "Página X de Y" (mismo mecanismo que en pdf()) =====
            $pdfConteo = Pdf::loadView('proformas.cadena_custodia', $data + ['totalPaginas' => 1]);
            $pdfConteo->setPaper('letter', 'landscape');
            $dompdfConteo = $pdfConteo->getDomPDF();
            $dompdfConteo->render();
            $totalPaginas = $dompdfConteo->getCanvas()->get_page_count();

            $pdf = Pdf::loadView('proformas.cadena_custodia', $data + ['totalPaginas' => $totalPaginas]);
            $pdf->setPaper('letter', 'landscape');

            return $pdf->stream("cadena-custodia-{$proforma->codigo}.pdf");

        } catch (\Exception $e) {
            Log::error('Error al generar Cadena de Custodia PDF: '.$e->getMessage());

            return back()->with('error', '❌ Error al generar Cadena de Custodia: '.$e->getMessage());
        }
    }

    private function agruparParametrosCadena($parametros)
    {
        $categorias = [
            'volumetria' => [],
            'ionometria' => [],
            'uv_visible' => [],
            'gravimetria' => [],
            'potenciometria' => [],
            'espectrofotometria' => [],
            'cromatografia' => [],
            'microbiologia' => [],
            'otros' => [],
        ];

        $metodos = [
            'Volumetria' => 'volumetria',
            'Ionometria' => 'ionometria',
            'UV - Visible' => 'uv_visible',
            'Gravimetria' => 'gravimetria',
            'Potenciometria' => 'potenciometria',
            'Espectrofotometria' => 'espectrofotometria',
            'Cromatografia' => 'cromatografia',
            'Microbiologia' => 'microbiologia',
        ];

        foreach ($parametros as $parametro) {
            $metodo = $parametro->metodo ?? $parametro->metodo_analitico ?? 'Otros';
            $categoriaClave = $metodos[$metodo] ?? 'otros';
            $categorias[$categoriaClave][] = [
                'nombre' => $parametro->nombre ?? $parametro->parametro ?? 'N/A',
                'unidad' => $parametro->unidad ?? '',
                'metodo' => $metodo,
                'precio' => $parametro->precio_unitario ?? 0,
            ];
        }

        return $categorias;
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