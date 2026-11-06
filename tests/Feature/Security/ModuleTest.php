<?php

namespace Tests\Feature\Security;

use App\Models\Module;
use App\Models\User;
use App\Policies\ModulePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ModuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $authorizedUser;

    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorizedUser = User::factory()->withPermissions([
            'modules.view',
        ])->create();

        $this->regularUser = User::factory()->create();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('security.modules.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authorized_user_can_view_modules_index(): void
    {
        Module::factory()->count(3)->create();

        $response = $this->actingAs($this->authorizedUser)->get(route('security.modules.index'));
        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('security/modules/pages/Index')
            ->where('routeName', 'security.modules.')
            ->has('modules.data')
            ->has('modules.meta')
        );
    }

    public function test_regular_user_cannot_view_modules_index(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('security.modules.index'));
        $response->assertForbidden();
    }

    public function test_module_creation_and_editing_routes_are_disabled(): void
    {
        $response = $this->actingAs($this->authorizedUser)->get('/security/modules/create');
        $response->assertNotFound();

        $response = $this->actingAs($this->authorizedUser)->post('/security/modules', [
            'name' => 'Reportes',
            'key' => 'rep',
        ]);
        $response->assertMethodNotAllowed();

        $module = Module::factory()->create();
        $response = $this->actingAs($this->authorizedUser)->get("/security/modules/{$module->id}/edit");
        $response->assertNotFound();

        $response = $this->actingAs($this->authorizedUser)->put("/security/modules/{$module->id}", [
            'name' => 'Reportes Modificado',
        ]);
        $response->assertNotFound();

        $response = $this->actingAs($this->authorizedUser)->delete("/security/modules/{$module->id}");
        $response->assertNotFound();
    }

    public function test_module_policy_denies_mutations(): void
    {
        $policy = new ModulePolicy;
        $module = Module::factory()->create();

        $this->assertFalse($policy->create($this->authorizedUser));
        $this->assertFalse($policy->update($this->authorizedUser, $module));
        $this->assertFalse($policy->delete($this->authorizedUser, $module));
        $this->assertFalse($policy->restore($this->authorizedUser, $module));
    }
}
