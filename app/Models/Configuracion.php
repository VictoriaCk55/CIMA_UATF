<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $table = 'configuraciones';

    protected $fillable = [
        'institucion_nombre',
        'laboratorio_nombre',
        'direccion',
        'telefono',
        'email',
        'logo_path',
        'footer_texto',
        'footer_direccion',
        'footer_telefono',
        'footer_email',
        'responsable_nombre',
        'responsable_cargo',
        'director_nombre',
        'director_cargo',
        'firma_path',
    ];

    /**
     * Obtener la configuración única del sistema
     */
    public static function obtener()
    {
        return static::first() ?? new static;
    }

    /**
     * Obtener el logo de la institución
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo_path) {
            return asset('storage/' . $this->logo_path);
        }
        return null;
    }

    /**
     * Obtener la firma
     */
    public function getFirmaUrlAttribute()
    {
        if ($this->firma_path) {
            return asset('storage/' . $this->firma_path);
        }
        return null;
    }

    /**
     * Obtener el nombre completo de la institución
     */
    public function getInstitucionCompletaAttribute()
    {
        $nombre = $this->institucion_nombre ?? 'CIMA-UATF';
        $sigla = $this->institucion_sigla ?? '';
        return $sigla ? $nombre . ' (' . $sigla . ')' : $nombre;
    }
}