<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Http\Requests\Security\RoleRequest;
use App\Http\Resources\Security\RoleResource;
use App\Models\Module;
use App\Traits\HasTableFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use HasTableFilters;

    protected string $source = 'security/roles/pages/';

    protected string $routeName = 'security.roles.';

    #[Authorize('viewAny', Role::class)]
    public function index(Request $request): Response
    {
        $filters = $this->getTableFilters($request, ['name', 'description', 'created_at']);

        $roles = Role::query()
            ->withCount(['permissions', 'users'])
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy($filters['order'], $filters['direction'])
            ->paginate($filters['rows'])
            ->withQueryString();

        return Inertia::render($this->source.'Index', [
            'title' => 'Roles',
            'roles' => RoleResource::collection($roles),
            'routeName' => $this->routeName,
            'filters' => $filters,
        ]);
    }

    #[Authorize('create', Role::class)]
    public function create(): Response
    {
        $modules = Module::with(['user'])->get();
        $permissions = Permission::all()->groupBy('module_key');

        return Inertia::render($this->source.'Create', [
            'title' => 'Crear Rol',
            'modules' => $modules,
            'groupedPermissions' => $permissions,
            'routeName' => $this->routeName,
        ]);
    }

    #[Authorize('create', Role::class)]
    public function store(RoleRequest $request): RedirectResponse
    {
        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'guard_name' => 'web',
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Rol creado exitosamente.',
        ]);

        return to_route("{$this->routeName}index");
    }

    #[Authorize('view', 'role')]
    public function show(Role $role): Response
    {
        $role->load(['permissions']);
        $role->loadCount(['permissions', 'users']);
        $modules = Module::all(['id', 'name', 'key', 'description']);
        $permissions = Permission::all()->groupBy('module_key');

        return Inertia::render($this->source.'Show', [
            'title' => 'Detalle del Rol',
            'role' => RoleResource::make($role),
            'modules' => $modules,
            'groupedPermissions' => $permissions,
            'routeName' => $this->routeName,
        ]);
    }

    #[Authorize('update', 'role')]
    public function edit(Role $role): Response
    {
        $role->load('permissions');
        $modules = Module::all();
        $permissions = Permission::all()->groupBy('module_key');

        return Inertia::render($this->source.'Edit', [
            'title' => 'Editar Rol',
            'role' => RoleResource::make($role),
            'modules' => $modules,
            'groupedPermissions' => $permissions,
            'routeName' => $this->routeName,
        ]);
    }

    #[Authorize('update', 'role')]
    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Rol actualizado exitosamente.',
        ]);

        return to_route("{$this->routeName}index");
    }

    #[Authorize('delete', 'role')]
    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Rol eliminado exitosamente.',
        ]);

        return to_route("{$this->routeName}index");
    }
}
