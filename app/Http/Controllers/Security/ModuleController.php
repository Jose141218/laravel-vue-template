<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Http\Requests\Security\ModuleRequest;
use App\Http\Resources\Security\ModuleResource;
use App\Models\Module;
use App\Traits\HasTableFilters;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Inertia\Inertia;
use Inertia\Response;

#[UsePolicy(Module::class)]
class ModuleController extends Controller
{
    use HasTableFilters;

    protected string $source = 'security/modules/pages/';

    protected string $routeName = 'security.modules.';

    #[Authorize('viewAny', Module::class)]
    public function index(Request $request): Response
    {
        $filters = $this->getTableFilters($request, ['name', 'key', 'description', 'created_at']);

        $modules = Module::query()
            ->select(['id', 'name', 'key', 'description', 'created_at'])
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('key', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy($filters['order'], $filters['direction'])
            ->paginate($filters['rows'])
            ->withQueryString();

        return Inertia::render("{$this->source}Index", [
            'title' => 'Módulos',
            'modules' => ModuleResource::collection($modules),
            'routeName' => $this->routeName,
            'filters' => $filters,
        ]);
    }

    #[Authorize('create', Module::class)]
    public function create(): Response
    {
        return Inertia::render("{$this->source}Create", [
            'title' => 'Crear Módulo',
            'routeName' => $this->routeName,
        ]);
    }

    #[Authorize('create', Module::class)]
    public function store(ModuleRequest $request): RedirectResponse
    {
        Module::create($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Módulo creado exitosamente.',
        ]);

        return to_route("{$this->routeName}index");
    }

    #[Authorize('update', 'module')]
    public function edit(Module $module): Response
    {
        return Inertia::render("{$this->source}Edit", [
            'title' => 'Editar Módulo',
            'module' => ModuleResource::make($module),
            'routeName' => $this->routeName,
        ]);
    }

    #[Authorize('update', 'module')]
    public function update(ModuleRequest $request, Module $module): RedirectResponse
    {
        $module->update($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Módulo actualizado exitosamente.',
        ]);

        return to_route("{$this->routeName}index");
    }

    #[Authorize('delete', 'module')]
    public function destroy(Module $module): RedirectResponse
    {
        $module->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Módulo eliminado exitosamente.',
        ]);

        return to_route("{$this->routeName}index");
    }
}
