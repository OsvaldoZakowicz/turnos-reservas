<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('services')->insert([
            [
                'name' => 'Pintura de Habitación Estándar',
                'description' => 'Pintura de paredes y techos para una habitación de hasta 15m². Incluye preparación básica de superficies.',
                'duration_minutes' => 240, // 4 horas
                'price' => 45.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pintura de Fachada Exterior',
                'description' => 'Aplicación de pintura impermeable en la fachada frontal de la vivienda (hasta 30m²).',
                'duration_minutes' => 360, // 6 horas
                'price' => 90.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mantenimiento de Jardín y Césped',
                'description' => 'Corte de césped, bordeado, limpieza de hojas y retiro de residuos vegetales en jardines medianos.',
                'duration_minutes' => 120, // 2 horas
                'price' => 30.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Poda de Setos y Arbustos',
                'description' => 'Poda de formación y mantenimiento para setos, cercos vivos y arbustos ornamentales.',
                'duration_minutes' => 180, // 3 horas
                'price' => 40.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Impermeabilización de Techos y Muros',
                'description' => 'Aplicación de membrana líquida impermeabilizante para prevenir filtraciones y humedad.',
                'duration_minutes' => 300, // 5 horas
                'price' => 75.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
