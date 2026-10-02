<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Admin role - full access (wildcard permission or all permissions)
        $adminRole = Role::updateOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['description' => 'Administrador con acceso total al sistema']
        );
        $adminRole->syncPermissions(Permission::all());

        // Coordinador role
        $coordinadorRole = Role::updateOrCreate(
            ['name' => 'coordinador', 'guard_name' => 'web'],
            ['description' => 'Coordinador de área con permisos de gestión']
        );
        $coordinadorRole->syncPermissions(
            Permission::whereIn('name', [
                'users.view',
                'roles.view',
                'permissions.view',
                'modules.view',
                'dashboard.view',
            ])->get()
        );

        // Usuario role
        $usuarioRole = Role::updateOrCreate(
            ['name' => 'usuario', 'guard_name' => 'web'],
            ['description' => 'Usuario estándar con acceso básico']
        );
        $usuarioRole->syncPermissions(
            Permission::whereIn('name', [
                'dashboard.view',
            ])->get()
        );
    }
}
