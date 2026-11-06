<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Http\Resources\Security\ModuleResource;
use App\Models\Module;
use App\Traits\HasTableFilters;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
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
}
