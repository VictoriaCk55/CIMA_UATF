<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'nombre',
        'codigo_documento',
        'version',
        'fecha_documento',
        'config',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'activo' => 'boolean',
        ];
    }

    /**
     * Obtener la ruta clave para el modelo
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Obtener un valor de configuración
     */
    public function config(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }

    /**
     * Establecer un valor de configuración
     */
    public function setConfig(string $key, mixed $value): self
    {
        $config = $this->config ?? [];
        data_set($config, $key, $value);
        $this->config = $config;
        return $this;
    }

    /**
     * Obtener el código completo del documento (con versión)
     */
    public function getCodigoCompletoAttribute(): string
    {
        $codigo = $this->codigo_documento ?? '';
        $version = $this->version ?? '';
        return $codigo . ($version ? ' V' . $version : '');
    }

    /**
     * Verificar si el documento es de tipo proforma
     */
    public function isProforma(): bool
    {
        return in_array($this->slug, ['solicitud-ensayo', 'solicitud-ensayo-ambiental']);
    }

    /**
     * Verificar si el documento tiene campos de cabecera
     */
    public function hasCabecera(): bool
    {
        return $this->slug === 'informe-resultados';
    }

    /**
     * Verificar si el documento tiene campos extendidos (notas, firmas)
     */
    public function hasExtras(): bool
    {
        $extrasSlugs = [
            'solicitud-ensayo',
            'solicitud-ensayo-ambiental',
            'informe-final',
            'informe-resultados',
            'cadena-custodia'
        ];
        return in_array($this->slug, $extrasSlugs);
    }

    /**
     * Obtener el tipo de proforma asociado
     */
    public function getTipoProformaAttribute(): ?string
    {
        return match($this->slug) {
            'solicitud-ensayo-ambiental' => 'AMBIENTAL',
            'solicitud-ensayo' => 'ANALISIS_QUIMICO,INVESTIGACION',
            default => null
        };
    }

    /**
     * Scope para documentos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para documentos por categoría
     */
    public function scopePorCategoria($query, $categoria)
    {
        $slugs = match($categoria) {
            'proformas' => ['solicitud-ensayo', 'solicitud-ensayo-ambiental'],
            'resultados' => ['informe-resultados'],
            'informes' => ['informe-final'],
            'custodia' => ['cadena-custodia'],
            default => []
        };
        return $query->whereIn('slug', $slugs);
    }
}