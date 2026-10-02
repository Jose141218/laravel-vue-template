<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Http\Requests\Security\UserRequest;
use App\Http\Resources\Security\UserResource;
use App\Models\User;
use App\Notifications\UserInvitationNotification;
use App\Traits\HasTableFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use HasTableFilters;

    protected string $source = 'security/users/pages/';

    protected string $routeName = 'security.users.';

    #[Authorize('viewAny', User::class)]
    public function index(Request $request): Response
    {
        $filters = [
            ...$this->getTableFilters($request, ['name', 'email', 'created_at']),
            'role' => $request->filled('role') ? (string) $request->input('role') : null,
        ];

        $users = User::query()
            ->withInvitationCreatedAt()
            ->with('roles')
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['role'], function ($query, $roleName) {
                $query->whereHas('roles', function ($q) use ($roleName) {
                    $q->where('name', $roleName);
                });
            })
            ->orderBy($filters['order'], $filters['direction'])
            ->paginate($filters['rows'])
            ->withQueryString();

        $roles = Role::all(['id', 'name']);

        return Inertia::render($this->source.'Index', [
            'title' => 'Usuarios',
            'users' => UserResource::collection($users),
            'roles' => $roles,
            'routeName' => $this->routeName,
            'filters' => $filters,
        ]);
    }

    #[Authorize('create', User::class)]
    public function create(): Response
    {
        $roles = Role::all(['id', 'name', 'description']);

        return Inertia::render($this->source.'Create', [
            'title' => 'Crear Usuario',
            'roles' => $roles,
            'routeName' => $this->routeName,
        ]);
    }

    #[Authorize('create', User::class)]
    public function store(UserRequest $request): RedirectResponse
    {
        $sendInvitation = $request->boolean('send_invitation');

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($sendInvitation ? Str::random(32) : (string) $request->password),
        ]);

        if (! $sendInvitation) {
            $user->email_verified_at = Carbon::now();
        }

        $user->save();

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        if ($sendInvitation) {
            $token = Password::broker()->createToken($user);
            $user->notify(new UserInvitationNotification($token));

            Inertia::flash('toast', [
                'type' => 'success',
                'message' => 'Usuario creado e invitación enviada por correo exitosamente.',
            ]);
        } else {
            Inertia::flash('toast', [
                'type' => 'success',
                'message' => 'Usuario creado exitosamente.',
            ]);
        }

        return to_route("{$this->routeName}index");
    }

    #[Authorize('view', 'user')]
    public function show(User $user): Response
    {
        $user->load('roles');

        return Inertia::render($this->source.'Show', [
            'title' => 'Detalle del Usuario',
            'user' => UserResource::make($user),
            'routeName' => $this->routeName,
        ]);
    }

    #[Authorize('update', 'user')]
    public function edit(User $user): Response
    {
        $user->load('roles');
        $roles = Role::all(['id', 'name', 'description']);

        return Inertia::render($this->source.'Edit', [
            'title' => 'Editar Usuario',
            'user' => UserResource::make($user),
            'roles' => $roles,
            'routeName' => $this->routeName,
        ]);
    }

    #[Authorize('update', 'user')]
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Usuario actualizado exitosamente.',
        ]);

        return to_route("{$this->routeName}index");
    }

    #[Authorize('delete', 'user')]
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Usuario eliminado exitosamente.',
        ]);

        return to_route("{$this->routeName}index");
    }

    #[Authorize('update', 'user')]
    public function resendInvitation(User $user): RedirectResponse
    {
        $token = Password::broker()->createToken($user);
        $user->notify(new UserInvitationNotification($token));

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => "Invitación reenviada exitosamente a {$user->email}.",
        ]);

        return back();
    }
}
