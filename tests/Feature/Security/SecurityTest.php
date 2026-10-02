<?php

namespace Tests\Feature\Security;

use App\Models\Module;
use App\Models\User;
use App\Notifications\UserInvitationNotification;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->adminUser = User::where('email', 'admin@sw.com')->firstOrFail();
        $this->regularUser = User::where('email', 'usuario@sw.com')->firstOrFail();
    }

    public function test_admin_can_view_modules_index(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('security.modules.index'));
        $response->assertOk();
    }

    public function test_regular_user_cannot_view_modules_index(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('security.modules.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_create_a_module(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('security.modules.store'), [
            'name' => 'Reportes',
            'key' => 'rep',
            'description' => 'Módulo de reportes y estadísticas',
        ]);

        $response->assertRedirect(route('security.modules.index'));
        $this->assertDatabaseHas('modules', [
            'key' => 'rep',
            'name' => 'Reportes',
        ]);
    }

    public function test_admin_can_view_roles_index(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('security.roles.index'));
        $response->assertOk();
    }

    public function test_admin_can_create_a_role_with_permissions(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('security.roles.store'), [
            'name' => 'editor',
            'description' => 'Editor de contenido',
            'permissions' => ['dashboard.view'],
        ]);

        $response->assertRedirect(route('security.roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'editor']);
        $role = Role::where('name', 'editor')->firstOrFail();
        $this->assertTrue($role->hasPermissionTo('dashboard.view'));
    }

    public function test_admin_cannot_delete_the_main_admin_role(): void
    {
        $adminRole = Role::where('name', 'admin')->firstOrFail();
        $response = $this->actingAs($this->adminUser)->delete(route('security.roles.destroy', $adminRole));
        $this->assertDatabaseHas('roles', ['name' => 'admin']);
    }

    public function test_admin_can_view_permissions_index(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('security.permissions.index'));
        $response->assertOk();
    }

    public function test_admin_can_create_a_permission(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('security.permissions.store'), [
            'name' => 'reports.export',
            'description' => 'Exportar reportes en Excel',
            'module_key' => 'sys',
        ]);

        $response->assertRedirect(route('security.permissions.index'));
        $this->assertDatabaseHas('permissions', ['name' => 'reports.export']);
    }

    public function test_admin_can_view_users_index(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('security.users.index'));
        $response->assertOk();
    }

    public function test_admin_can_create_a_user_with_roles(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('security.users.store'), [
            'name' => 'Nuevo Colaborador',
            'email' => 'colaborador@sw.com',
            'password' => 'password123',
            'roles' => ['coordinador'],
        ]);

        $response->assertRedirect(route('security.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'colaborador@sw.com']);
        $newUser = User::where('email', 'colaborador@sw.com')->firstOrFail();
        $this->assertTrue($newUser->hasRole('coordinador'));
        $this->assertNotNull($newUser->email_verified_at);
    }

    public function test_admin_can_create_user_with_queued_invitation_email(): void
    {
        Notification::fake();

        $response = $this->actingAs($this->adminUser)->post(route('security.users.store'), [
            'name' => 'Invitado Nuevo',
            'email' => 'invitado@sw.com',
            'send_invitation' => true,
            'roles' => ['coordinador'],
        ]);

        $response->assertRedirect(route('security.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'invitado@sw.com',
            'email_verified_at' => null,
        ]);

        $invitedUser = User::where('email', 'invitado@sw.com')->firstOrFail();
        $this->assertTrue($invitedUser->hasRole('coordinador'));

        Notification::assertSentTo($invitedUser, UserInvitationNotification::class);
    }

    public function test_admin_can_resend_invitation_to_user(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'resend@sw.com',
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('security.users.resend-invitation', $user));

        $response->assertRedirect();
        Notification::assertSentTo($user, UserInvitationNotification::class);
    }

    public function test_invited_user_can_set_password_and_activate_account(): void
    {
        $user = User::factory()->create([
            'email' => 'activate@sw.com',
            'email_verified_at' => null,
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->get(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
            'invitation' => 1,
        ]));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('auth/pages/ResetPassword')
            ->where('isInvitation', true)
            ->where('email', $user->email)
        );

        $updateResponse = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewSecretPassword123!',
            'password_confirmation' => 'NewSecretPassword123!',
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $updateResponse->assertRedirect(route('login'));

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue(Hash::check('NewSecretPassword123!', $user->password));

        // Ensure user can now log in
        $loginResponse = $this->post(route('login'), [
            'email' => 'activate@sw.com',
            'password' => 'NewSecretPassword123!',
        ]);

        $loginResponse->assertRedirect(route('dashboard'));
    }

    public function test_user_invitation_statuses_are_correctly_determined(): void
    {
        // 1. Active user
        $activeUser = User::factory()->create([
            'email_verified_at' => Carbon::now(),
        ]);
        $this->assertSame('active', $activeUser->getInvitationStatus());

        // 2. Pending user (token issued now)
        $pendingUser = User::factory()->create([
            'email' => 'pending@sw.com',
            'email_verified_at' => null,
        ]);
        Password::broker()->createToken($pendingUser);
        $this->assertSame('pending', $pendingUser->getInvitationStatus());

        // 3. Expired user (token issued 25 hours ago)
        $expiredUser = User::factory()->create([
            'email' => 'expired@sw.com',
            'email_verified_at' => null,
        ]);
        DB::table('password_reset_tokens')->insert([
            'email' => $expiredUser->email,
            'token' => 'dummy-hash',
            'created_at' => Carbon::now()->subHours(25),
        ]);
        $this->assertSame('expired', $expiredUser->getInvitationStatus());

        // 4. Expired user (unverified with no token in table)
        $noTokenUser = User::factory()->create([
            'email' => 'notoken@sw.com',
            'email_verified_at' => null,
        ]);
        $this->assertSame('expired', $noTokenUser->getInvitationStatus());
    }

    public function test_users_index_includes_invitation_status_in_resource(): void
    {
        $pendingUser = User::factory()->create([
            'email' => 'list_pending@sw.com',
            'email_verified_at' => null,
        ]);
        Password::broker()->createToken($pendingUser);

        $response = $this->actingAs($this->adminUser)->get(route('security.users.index'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('security/users/pages/Index')
            ->has('users.data', fn (AssertableInertia $data) => $data
                ->where('0.status', fn ($status) => in_array($status, ['active', 'pending', 'expired'], true))
                ->etc()
            )
        );
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $response = $this->actingAs($this->adminUser)->delete(route('security.users.destroy', $this->adminUser));
        $this->assertDatabaseHas('users', ['id' => $this->adminUser->id]);
    }

    public function test_security_controllers_provide_route_name_with_trailing_dot_and_resources(): void
    {
        $this->actingAs($this->adminUser)->get(route('security.modules.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('security/modules/pages/Index')
                ->where('routeName', 'security.modules.')
                ->has('modules.data')
                ->has('modules.meta')
            );

        $this->actingAs($this->adminUser)->get(route('security.permissions.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('security/permissions/pages/Index')
                ->where('routeName', 'security.permissions.')
                ->has('permissions.data')
                ->has('permissions.meta')
            );

        $this->actingAs($this->adminUser)->get(route('security.roles.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('security/roles/pages/Index')
                ->where('routeName', 'security.roles.')
                ->has('roles.data')
                ->has('roles.meta')
            );

        $this->actingAs($this->adminUser)->get(route('security.users.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('security/users/pages/Index')
                ->where('routeName', 'security.users.')
                ->has('users.data')
                ->has('users.meta')
            );
    }

    public function test_security_controllers_edit_methods_provide_unwrapped_resources(): void
    {
        $module = Module::firstOrFail();
        $this->actingAs($this->adminUser)->get(route('security.modules.edit', $module))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('security/modules/pages/Edit')
                ->where('routeName', 'security.modules.')
                ->where('module.name', $module->name)
                ->where('module.key', $module->key)
                ->missing('module.data')
            );

        $permission = Permission::firstOrFail();
        $this->actingAs($this->adminUser)->get(route('security.permissions.edit', $permission))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('security/permissions/pages/Edit')
                ->where('routeName', 'security.permissions.')
                ->where('permission.name', $permission->name)
                ->missing('permission.data')
            );

        $role = Role::firstOrFail();
        $this->actingAs($this->adminUser)->get(route('security.roles.edit', $role))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('security/roles/pages/Edit')
                ->where('routeName', 'security.roles.')
                ->where('role.name', $role->name)
                ->missing('role.data')
            );

        $this->actingAs($this->adminUser)->get(route('security.users.edit', $this->regularUser))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('security/users/pages/Edit')
                ->where('routeName', 'security.users.')
                ->where('user.name', $this->regularUser->name)
                ->where('user.email', $this->regularUser->email)
                ->missing('user.data')
            );
    }

    public function test_admin_can_view_role_show(): void
    {
        $role = Role::firstOrFail();
        $response = $this->actingAs($this->adminUser)->get(route('security.roles.show', $role));
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
        $role = Role::firstOrFail();
        $response = $this->actingAs($this->regularUser)->get(route('security.roles.show', $role));
        $response->assertForbidden();
    }

    public function test_admin_can_view_user_show(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('security.users.show', $this->regularUser));
        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('security/users/pages/Show')
            ->where('routeName', 'security.users.')
            ->where('user.name', $this->regularUser->name)
            ->where('user.email', $this->regularUser->email)
            ->missing('user.data')
        );
    }

    public function test_regular_user_cannot_view_user_show(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('security.users.show', $this->adminUser));
        $response->assertForbidden();
    }
}
