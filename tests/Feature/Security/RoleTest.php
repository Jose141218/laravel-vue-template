<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    protected User $authorizedUser;

    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorizedUser = User::factory()->withPermissions([
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
        ])->create();

        $this->regularUser = User::factory()->create();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('security.roles.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authorized_user_can_view_roles_index(): void
    {
        Role::create(['name' => 'editor', 'guard_name' => 'web']);

        $response = $this->actingAs($this->authorizedUser)->get(route('security.roles.index'));
        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('security/roles/pages/Index')
            ->where('routeName', 'security.roles.')
            ->has('roles.data')
            ->has('roles.meta')
        );
    }

    public function test_regular_user_cannot_view_roles_index(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('security.roles.index'));
        $response->assertForbidden();
    }

    public function test_authorized_user_can_create_a_role_with_permissions(): void
    {
        Permission::create(['name' => 'dashboard.view', 'guard_name' => 'web']);

        $response = $this->actingAs($this->authorizedUser)->post(route('security.roles.store'), [
            'name' => 'editor',
            'description' => 'Editor de contenido',
            'permissions' => ['dashboard.view'],
        ]);

        $response->assertRedirect(route('security.roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'editor']);
        $role = Role::where('name', 'editor')->firstOrFail();
        $this->assertTrue($role->hasPermissionTo('dashboard.view'));
    }

    public function test_authorized_user_cannot_create_role_with_missing_name(): void
    {
        $response = $this->actingAs($this->authorizedUser)->post(route('security.roles.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_authorized_user_cannot_create_role_with_duplicate_name(): void
    {
        Role::create(['name' => 'editor', 'guard_name' => 'web']);

        $response = $this->actingAs($this->authorizedUser)->post(route('security.roles.store'), [
            'name' => 'editor',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_authorized_user_cannot_delete_the_main_admin_role(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $response = $this->actingAs($this->authorizedUser)->delete(route('security.roles.destroy', $adminRole));
        $response->assertForbidden();
        $this->assertDatabaseHas('roles', ['name' => 'admin']);
    }

    public function test_authorized_user_cannot_view_edit_page_for_admin_role(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $response = $this->actingAs($this->authorizedUser)->get(route('security.roles.edit', $adminRole));
        $response->assertForbidden();
    }

    public function test_authorized_user_cannot_update_admin_role(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $response = $this->actingAs($this->authorizedUser)->put(route('security.roles.update', $adminRole), [
            'name' => 'admin-renamed',
            'description' => 'Nuevo intento de descripción',
        ]);
        $response->assertForbidden();
        $this->assertDatabaseHas('roles', ['name' => 'admin']);
        $this->assertDatabaseMissing('roles', ['name' => 'admin-renamed']);
    }

    public function test_authorized_user_can_view_role_show(): void
    {
        $role = Role::create(['name' => 'supervisor', 'guard_name' => 'web']);
        $response = $this->actingAs($this->authorizedUser)->get(route('security.roles.show', $role));
        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('security/roles/pages/Show')
            ->where('routeName', 'security.roles.')
            ->where('role.name', $role->name)
            ->has('modules')
            ->has('groupedPermissions')
            ->missing('role.data')
        );
    }

    public function test_regular_user_cannot_view_role_show(): void
    {
        $role = Role::create(['name' => 'supervisor', 'guard_name' => 'web']);
        $response = $this->actingAs($this->regularUser)->get(route('security.roles.show', $role));
        $response->assertForbidden();
    }

    public function test_authorized_user_can_view_edit_role_with_unwrapped_resource(): void
    {
        $role = Role::create(['name' => 'supervisor', 'guard_name' => 'web']);
        $response = $this->actingAs($this->authorizedUser)->get(route('security.roles.edit', $role));
        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('security/roles/pages/Edit')
            ->where('routeName', 'security.roles.')
            ->where('role.name', $role->name)
            ->missing('role.data')
        );
    }
}
