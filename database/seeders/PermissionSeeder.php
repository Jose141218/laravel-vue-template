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
                ],
                'modules' => [
                    'view' => 'Ver lista de módulos',
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

        $validPermissions = [];

        foreach ($modules as $moduleKey => $entities) {
            foreach ($entities as $entity => $actions) {
                foreach ($actions as $action => $description) {
                    $permissionName = "{$entity}.{$action}";
                    $validPermissions[] = $permissionName;

                    Permission::updateOrCreate(
                        ['name' => $permissionName, 'guard_name' => 'web'],
                        [
                            'description' => $description,
                            'module_key' => $moduleKey,
                        ]
                    );
                }
            }
        }

        // Delete obsolete permissions no longer defined in code
        Permission::where('guard_name', 'web')
            ->whereNotIn('name', $validPermissions)
            ->delete();
    }
}
