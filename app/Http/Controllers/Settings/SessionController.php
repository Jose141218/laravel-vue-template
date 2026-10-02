<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\OtherSessionsDeleteRequest;
use App\Http\Resources\Settings\SessionResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SessionController extends Controller
{
    protected string $source = 'settings/pages/';

    public function index(Request $request): Response
    {
        $driver = config('session.driver');
        $isDatabaseDriver = $driver === 'database';

        $sessions = [];

        if ($isDatabaseDriver) {
            $rawSessions = DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', $request->user()->getAuthIdentifier())
                ->orderBy('last_activity', 'desc')
                ->get();

            $sessions = SessionResource::collection($rawSessions)->resolve();
        }

        return Inertia::render($this->source.'Sessions', [
            'sessions' => $sessions,
            'isDatabaseDriver' => $isDatabaseDriver,
        ]);
    }

    public function destroy(Request $request, string $session): RedirectResponse
    {
        if (config('session.driver') !== 'database') {
            return back();
        }

        $currentSessionId = $request->session()->getId();

        DB::connection(config('session.connection'))
            ->table(config('session.table', 'sessions'))
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->where('id', $session)
            ->delete();

        if ($session === $currentSessionId) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/');
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Session revoked successfully.'),
        ]);

        return back();
    }

    public function destroyOther(OtherSessionsDeleteRequest $request): RedirectResponse
    {
        if (config('session.driver') === 'database') {
            DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', $request->user()->getAuthIdentifier())
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        }

        Auth::logoutOtherDevices($request->password);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Other browser sessions logged out successfully.'),
        ]);

        return back();
    }
}
