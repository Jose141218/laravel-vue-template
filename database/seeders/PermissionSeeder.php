<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = [
            'seg' => [
                'users' => [
                    'view' => 'Ver lista de usuarios',
                    'create' => 'Crear nuevos usuarios',
                    'edit' => 'Editar usuarios existentes',
                    'delete' => 'Eliminar usuarios',
                    'restore' => 'Restaurar usuarios eliminados',
                ],
                'roles' => [
                    'view' => 'Ver lista de roles',
                    'create' => 'Crear nuevos roles',
                    'edit' => 'Editar roles existentes',
                    'delete' => 'Eliminar roles',
                ],
                'permissions' => [
                    'view' => 'Ver lista de permisos',
                    'create' => 'Crear nuevos permisos',
                    'edit' => 'Editar permisos existentes',
                    'delete' => 'Eliminar permisos',
                ],
                'modules' => [
                    'view' => 'Ver lista de módulos',
                    'create' => 'Crear nuevos módulos',
                    'edit' => 'Editar módulos existentes',
                    'delete' => 'Eliminar módulos',
                    'restore' => 'Restaurar módulos eliminados',
                ],
            ],
            'dash' => [
                'dashboard' => [
                    'view' => 'Acceso al panel principal',
                ],
            ],
            'sys' => [
                'settings' => [
                    'view' => 'Ver configuración del sistema',
                    'update' => 'Actualizar configuración del sistema',
                ],
            ],
        ];

        foreach ($modules as $moduleKey => $entities) {
            foreach ($entities as $entity => $actions) {
                foreach ($actions as $action => $description) {
                    Permission::updateOrCreate(
                        ['name' => "{$entity}.{$action}", 'guard_name' => 'web'],
                        [
                            'description' => $description,
                            'module_key' => $moduleKey,
                        ]
                    );
                }
            }
        }
    }
}
