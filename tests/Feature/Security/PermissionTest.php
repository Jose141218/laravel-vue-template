<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Policies\PermissionPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $authorizedUser;

    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorizedUser = User::factory()->withPermissions([
            'permissions.view',
        ])->create();

        $this->regularUser = User::factory()->create();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('security.permissions.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authorized_user_can_view_permissions_index(): void
    {
        Permission::create(['name' => 'reports.view', 'guard_name' => 'web']);

        $response = $this->actingAs($this->authorizedUser)->get(route('security.permissions.index'));
        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('security/permissions/pages/Index')
            ->where('routeName', 'security.permissions.')
            ->has('permissions.data')
            ->has('permissions.meta')
        );
    }

    public function test_regular_user_cannot_view_permissions_index(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('security.permissions.index'));
        $response->assertForbidden();
    }

    public function test_permission_creation_and_editing_routes_are_disabled(): void
    {
        $response = $this->actingAs($this->authorizedUser)->get('/security/permissions/create');
        $response->assertNotFound();

        $response = $this->actingAs($this->authorizedUser)->post('/security/permissions', [
            'name' => 'test.permission',
        ]);
        $response->assertMethodNotAllowed();

        $permission = Permission::create(['name' => 'test.view', 'guard_name' => 'web']);
        $response = $this->actingAs($this->authorizedUser)->get("/security/permissions/{$permission->id}/edit");
        $response->assertNotFound();

        $response = $this->actingAs($this->authorizedUser)->put("/security/permissions/{$permission->id}", [
            'name' => 'test.updated',
        ]);
        $response->assertNotFound();

        $response = $this->actingAs($this->authorizedUser)->delete("/security/permissions/{$permission->id}");
        $response->assertNotFound();
    }

    public function test_permission_policy_denies_mutations(): void
    {
        $policy = new PermissionPolicy;
        $permission = Permission::create(['name' => 'sample.view', 'guard_name' => 'web']);

        $this->assertFalse($policy->create($this->authorizedUser));
        $this->assertFalse($policy->update($this->authorizedUser, $permission));
        $this->assertFalse($policy->delete($this->authorizedUser, $permission));
    }
}
