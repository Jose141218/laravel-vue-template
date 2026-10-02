<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Http\Requests\Security\PermissionRequest;
use App\Http\Resources\Security\PermissionResource;
use App\Models\Module;
use App\Traits\HasTableFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    use HasTableFilters;

    protected string $source = 'security/permissions/pages/';

    protected string $routeName = 'security.permissions.';

    #[Authorize('viewAny', Permission::class)]
    public function index(Request $request): Response
    {
        $filters = [
            ...$this->getTableFilters($request, ['name', 'description', 'module_key', 'created_at']),
            'module_key' => $request->filled('module_key') ? (string) $request->input('module_key') : null,
        ];

        $permissions = Permission::query()
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['module_key'], function ($query, $moduleKey) {
                $query->where('module_key', $moduleKey);
            })
            ->orderBy($filters['order'], $filters['direction'])
            ->paginate($filters['rows'])
            ->withQueryString();

        $modules = Module::all(['key', 'name']);

        return Inertia::render($this->source.'Index', [
            'title' => 'Permisos',
            'permissions' => PermissionResource::collection($permissions),
            'modules' => $modules,
            'routeName' => $this->routeName,
            'filters' => $filters,
        ]);
    }

    #[Authorize('create', Permission::class)]
    public function create(): Response
    {
        $modules = Module::all(['key', 'name']);

        return Inertia::render($this->source.'Create', [
            'title' => 'Crear Permiso',
            'modules' => $modules,
            'routeName' => $this->routeName,
        ]);
    }

    #[Authorize('create', Permission::class)]
    public function store(PermissionRequest $request): RedirectResponse
    {
        Permission::create([
            'name' => $request->name,
            'description' => $request->description,
            'module_key' => $request->module_key,
            'guard_name' => 'web',
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Permiso creado exitosamente.',
        ]);

        return to_route("{$this->routeName}index");
    }

    #[Authorize('update', 'permission')]
    public function edit(Permission $permission): Response
    {
        $modules = Module::all(['key', 'name']);

        return Inertia::render($this->source.'Edit', [
            'title' => 'Editar Permiso',
            'permission' => PermissionResource::make($permission),
            'modules' => $modules,
            'routeName' => $this->routeName,
        ]);
    }

    #[Authorize('update', 'permission')]
    public function update(PermissionRequest $request, Permission $permission): RedirectResponse
    {
        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
            'module_key' => $request->module_key,
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Permiso actualizado exitosamente.',
        ]);

        return to_route("{$this->routeName}index");
    }

    #[Authorize('delete', 'permission')]
    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Permiso eliminado exitosamente.',
        ]);

        return to_route("{$this->routeName}index");
    }
}
