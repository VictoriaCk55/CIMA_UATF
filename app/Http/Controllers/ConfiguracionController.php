<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(?Documento $documento = null)
    {
        $documentos = Documento::where('activo', true)->get();

        if ($documento === null) {
            $documento = $documentos->first();
        }

        return view('configuraciones.index', compact('documentos', 'documento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Documento $documento)
    {
        $validated = $request->validate([
            // Campos del documento
            'codigo_documento' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:255',
            'fecha_documento' => 'nullable|string|max:255',
            
            // Campos de configuración
            'institucion_nombre' => 'nullable|string|max:255',
            'universidad_nombre' => 'nullable|string|max:255',
            'institucion_sigla' => 'nullable|string|max:50',
            'laboratorio_nombre' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            
            // Pie de página
            'footer_texto' => 'nullable|string|max:500',
            'footer_direccion' => 'nullable|string|max:255',
            'footer_telefono' => 'nullable|string|max:255',
            'footer_email' => 'nullable|email|max:255',
            
            // Firmas
            'responsable_nombre' => 'nullable|string|max:255',
            'responsable_cargo' => 'nullable|string|max:255',
            'director_nombre' => 'nullable|string|max:255',
            'director_cargo' => 'nullable|string|max:255',
            
            // Notas
            'nota1' => 'nullable|string|max:500',
            'nota2' => 'nullable|string|max:500',
            'nota3' => 'nullable|string|max:500',
        ]);

        // Obtener configuración actual
        $config = $documento->config ?? [];

        // Actualizar campos del documento
        foreach (['codigo_documento', 'version', 'fecha_documento'] as $field) {
            if (array_key_exists($field, $validated)) {
                $documento->$field = $validated[$field];
            }
        }

        // Actualizar campos de configuración
        $configFields = [
            'institucion_nombre', 'universidad_nombre', 'institucion_sigla',
            'laboratorio_nombre', 'direccion', 'telefono', 'email',
            'footer_texto', 'footer_direccion', 'footer_telefono', 'footer_email',
            'responsable_nombre', 'responsable_cargo',
            'director_nombre', 'director_cargo',
            'nota1', 'nota2', 'nota3'
        ];

        foreach ($configFields as $field) {
            if (array_key_exists($field, $validated)) {
                $config[$field] = $validated[$field];
            }
        }

        // Manejar logo
        if ($request->hasFile('logo')) {
            $request->validate([
                'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
            ]);
            
            // Eliminar logo anterior si existe
            if (!empty($config['logo_path']) && Storage::disk('public')->exists($config['logo_path'])) {
                Storage::disk('public')->delete($config['logo_path']);
            }
            
            $path = $request->file('logo')->store('documentos', 'public');
            $config['logo_path'] = $path;
        }

        // Manejar firma
        if ($request->hasFile('firma')) {
            $request->validate([
                'firma' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
            ]);
            
            // Eliminar firma anterior si existe
            if (!empty($config['firma_path']) && Storage::disk('public')->exists($config['firma_path'])) {
                Storage::disk('public')->delete($config['firma_path']);
            }
            
            $path = $request->file('firma')->store('documentos', 'public');
            $config['firma_path'] = $path;
        }

        // Guardar configuración
        $documento->config = $config;
        $documento->save();

        return redirect()->route('configuraciones.index', $documento->slug)
            ->with('success', "✅ Configuración de «{$documento->nombre}» guardada exitosamente.");
    }

    /**
     * Obtener la configuración para un tipo de documento específico
     * (Método auxiliar para usar en otros controladores)
     */
    public static function getConfigForDocument(string $slug): array
    {
        $documento = Documento::where('slug', $slug)->first();
        if (!$documento) {
            return [];
        }
        return $documento->config ?? [];
    }

    /**
     * Obtener la configuración global (logo, firma, datos institucionales)
     * (Método auxiliar para usar en otros controladores)
     */
    public static function getGlobalConfig(): array
    {
        $config = \App\Models\Configuracion::obtener();
        return [
            'institucion_nombre' => $config->institucion_nombre,
            'laboratorio_nombre' => $config->laboratorio_nombre,
            'direccion' => $config->direccion,
            'telefono' => $config->telefono,
            'email' => $config->email,
            'logo_url' => $config->logo_url,
            'firma_url' => $config->firma_url,
        ];
    }
}