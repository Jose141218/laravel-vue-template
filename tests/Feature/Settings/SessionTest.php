<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SessionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['session.driver' => 'database']);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('sessions.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_sessions_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $currentSessionId = 'current_session_123';
        $otherSessionId = 'other_session_456';

        DB::table('sessions')->insert([
            [
                'id' => $currentSessionId,
                'user_id' => $user->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'payload' => serialize(['user' => $user->id]),
                'last_activity' => time(),
            ],
            [
                'id' => $otherSessionId,
                'user_id' => $user->id,
                'ip_address' => '192.168.1.50',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
                'payload' => serialize(['user' => $user->id]),
                'last_activity' => time() - 3600,
            ],
        ]);

        $response = $this->actingAs($user)
            ->withSession(['_token' => 'test-token'])
            ->get(route('sessions.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('settings/pages/Sessions')
            ->has('sessions', 2)
            ->where('isDatabaseDriver', true)
            ->where('sessions.0.id', $currentSessionId)
            ->where('sessions.0.agent.platform', 'Windows')
            ->where('sessions.0.agent.browser', 'Chrome')
            ->where('sessions.0.agent.is_desktop', true)
            ->where('sessions.1.id', $otherSessionId)
            ->where('sessions.1.agent.platform', 'iOS')
            ->where('sessions.1.agent.browser', 'Safari')
            ->where('sessions.1.agent.is_mobile', true)
        );
    }

    public function test_sessions_page_handles_non_database_driver(): void
    {
        config(['session.driver' => 'file']);

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('sessions.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('settings/pages/Sessions')
            ->has('sessions', 0)
            ->where('isDatabaseDriver', false)
        );
    }

    public function test_user_can_revoke_a_specific_session(): void
    {
        $user = User::factory()->create();
        $otherSessionId = 'other_session_to_delete';

        DB::table('sessions')->insert([
            'id' => $otherSessionId,
            'user_id' => $user->id,
            'ip_address' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
            'payload' => 'dummy_payload',
            'last_activity' => time(),
        ]);

        $this->assertDatabaseHas('sessions', ['id' => $otherSessionId]);

        $response = $this->actingAs($user)
            ->delete(route('sessions.destroy', $otherSessionId));

        $response->assertRedirect();
        $this->assertDatabaseMissing('sessions', ['id' => $otherSessionId]);
    }

    public function test_user_cannot_revoke_another_users_session(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $sessionBId = 'user_b_session';

        DB::table('sessions')->insert([
            'id' => $sessionBId,
            'user_id' => $userB->id,
            'ip_address' => '10.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Linux; Android 14)',
            'payload' => 'dummy_payload',
            'last_activity' => time(),
        ]);

        $response = $this->actingAs($userA)
            ->delete(route('sessions.destroy', $sessionBId));

        $response->assertRedirect();
        $this->assertDatabaseHas('sessions', ['id' => $sessionBId, 'user_id' => $userB->id]);
    }

    public function test_user_can_logout_other_browser_sessions_with_valid_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $currentSessionId = 'my_current_session';
        $remoteSessionId1 = 'remote_session_1';
        $remoteSessionId2 = 'remote_session_2';

        DB::table('sessions')->insert([
            [
                'id' => $currentSessionId,
                'user_id' => $user->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Browser 1',
                'payload' => 'payload1',
                'last_activity' => time(),
            ],
            [
                'id' => $remoteSessionId1,
                'user_id' => $user->id,
                'ip_address' => '192.168.1.1',
                'user_agent' => 'Browser 2',
                'payload' => 'payload2',
                'last_activity' => time() - 100,
            ],
            [
                'id' => $remoteSessionId2,
                'user_id' => $user->id,
                'ip_address' => '192.168.1.2',
                'user_agent' => 'Browser 3',
                'payload' => 'payload3',
                'last_activity' => time() - 200,
            ],
        ]);

        $response = $this->actingAs($user)
            ->withSession(['_token' => 'dummy'])
            ->delete(route('sessions.destroy-other'), [
                'password' => 'password123',
            ]);

        $response->assertRedirect();

        // The remote sessions should be deleted from DB
        $this->assertDatabaseMissing('sessions', ['id' => $remoteSessionId1]);
        $this->assertDatabaseMissing('sessions', ['id' => $remoteSessionId2]);
    }

    public function test_cannot_logout_other_sessions_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $remoteSessionId = 'remote_session_to_keep';

        DB::table('sessions')->insert([
            'id' => $remoteSessionId,
            'user_id' => $user->id,
            'ip_address' => '192.168.1.1',
            'user_agent' => 'Browser 2',
            'payload' => 'payload2',
            'last_activity' => time(),
        ]);

        $response = $this->actingAs($user)
            ->from(route('sessions.index'))
            ->delete(route('sessions.destroy-other'), [
                'password' => 'wrong-password',
            ]);

        $response->assertRedirect(route('sessions.index'));
        $response->assertSessionHasErrors('password');

        $this->assertDatabaseHas('sessions', ['id' => $remoteSessionId]);
    }
}
