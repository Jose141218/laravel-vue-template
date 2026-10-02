<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'name' => 'Seguridad',
                'description' => 'Gestión de usuarios, roles, permisos y módulos del sistema',
                'key' => 'seg',
            ],
            [
                'name' => 'Sistema',
                'description' => 'Configuración y ajustes generales del sistema',
                'key' => 'sys',
            ],
            [
                'name' => 'Dashboard',
                'description' => 'Métricas, resúmenes y accesos directos',
                'key' => 'dash',
            ],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['key' => $module['key']],
                $module
            );
        }
    }
}
