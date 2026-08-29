<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use Illuminate\Database\Seeder;

class ConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar si ya existe configuración
        if (Configuracion::count() > 0) {
            $this->command->info('La configuración ya existe. No se insertaron datos duplicados.');
            return;
        }

        Configuracion::create([
            'institucion_nombre' => 'Centro de Investigación Minero Ambiental',
            'laboratorio_nombre' => 'CENTRO DE INVESTIGACIÓN MINERO AMBIENTAL',
            'direccion' => 'Av. Arce esq. Villazón s/n, Potosí - Bolivia',
            'telefono' => '(591) 2 6234567',
            'email' => 'cima@uatf.edu.bo',
            'logo_path' => null,
            'firma_path' => null,
            'footer_texto' => 'Unidad de Investigación Ambiental - CIMA UATF',
            'footer_direccion' => 'Av. Arce esq. Villazón s/n',
            'footer_telefono' => '62-29711',
            'footer_email' => 'cima-uatf@uatf.edu.bo',
            'responsable_nombre' => 'Lic. Mayra Anghela Calderón Rosas',
            'responsable_cargo' => 'RESPONSABLE - UAQ',
            'director_nombre' => 'M.Sc. Ing. Elva Fernández I.',
            'director_cargo' => 'DIRECTOR(A) CIMA - UATF',
            'nota1' => 'Para realizar el análisis se debe dejar cancelado el 100% del monto total.',
            'nota2' => 'El laboratorio no realiza declaraciones de conformidad sobre los resultados que se reportan.',
            'nota3' => 'Los resultados estarán disponibles dentro de los plazos establecidos según el tipo de análisis.',
        ]);

        $this->command->info('✅ Configuración creada exitosamente.');
    }
}