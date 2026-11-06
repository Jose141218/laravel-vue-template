<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Notifications\UserInvitationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected User $authorizedUser;

    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorizedUser = User::factory()->withPermissions([
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
        ])->create();

        $this->regularUser = User::factory()->create();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('security.users.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_factory_states_assign_roles_and_permissions(): void
    {
        $admin = User::factory()->admin()->create();
        $this->assertTrue($admin->hasRole('admin'));

        $coordinator = User::factory()->withRole('coordinador')->create();
        $this->assertTrue($coordinator->hasRole('coordinador'));

        $permittedUser = User::factory()->withPermissions(['custom.access'])->create();
        $this->assertTrue($permittedUser->can('custom.access'));
    }

    public function test_authorized_user_can_view_users_index(): void
    {
        $response = $this->actingAs($this->authorizedUser)->get(route('security.users.index'));
        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('security/users/pages/Index')
            ->where('routeName', 'security.users.')
            ->has('users.data')
            ->has('users.meta')
        );
    }

    public function test_regular_user_cannot_view_users_index(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('security.users.index'));
        $response->assertForbidden();
    }

    public function test_authorized_user_can_create_a_user_with_roles(): void
    {
        Role::firstOrCreate(['name' => 'coordinador', 'guard_name' => 'web']);

        $response = $this->actingAs($this->authorizedUser)->post(route('security.users.store'), [
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

    public function test_authorized_user_cannot_create_user_with_missing_fields(): void
    {
        $response = $this->actingAs($this->authorizedUser)->post(route('security.users.store'), [
            'name' => '',
            'email' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email']);
    }

    public function test_authorized_user_cannot_create_user_with_duplicate_email(): void
    {
        $response = $this->actingAs($this->authorizedUser)->post(route('security.users.store'), [
            'name' => 'Duplicado',
            'email' => $this->regularUser->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_authorized_user_can_create_user_with_queued_invitation_email(): void
    {
        Notification::fake();
        Role::firstOrCreate(['name' => 'coordinador', 'guard_name' => 'web']);

        $response = $this->actingAs($this->authorizedUser)->post(route('security.users.store'), [
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

    public function test_authorized_user_can_resend_invitation_to_user(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'resend@sw.com',
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($this->authorizedUser)->post(route('security.users.resend-invitation', $user));

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

        $response = $this->actingAs($this->authorizedUser)->get(route('security.users.index'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('security/users/pages/Index')
            ->has('users.data', fn (AssertableInertia $data) => $data
                ->where('0.status', fn ($status) => in_array($status, ['active', 'pending', 'expired'], true))
                ->etc()
            )
        );
    }

    public function test_authorized_user_cannot_delete_themselves(): void
    {
        $response = $this->actingAs($this->authorizedUser)->delete(route('security.users.destroy', $this->authorizedUser));
        $response->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $this->authorizedUser->id]);
    }

    public function test_authorized_user_can_view_user_show(): void
    {
        $response = $this->actingAs($this->authorizedUser)->get(route('security.users.show', $this->regularUser));
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
        $response = $this->actingAs($this->regularUser)->get(route('security.users.show', $this->authorizedUser));
        $response->assertForbidden();
    }

    public function test_authorized_user_can_view_edit_user_with_unwrapped_resource(): void
    {
        $response = $this->actingAs($this->authorizedUser)->get(route('security.users.edit', $this->regularUser));
        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('security/users/pages/Edit')
            ->where('routeName', 'security.users.')
            ->where('user.name', $this->regularUser->name)
            ->where('user.email', $this->regularUser->email)
            ->missing('user.data')
        );
    }
}
