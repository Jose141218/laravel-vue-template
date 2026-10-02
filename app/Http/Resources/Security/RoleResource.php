<?php

namespace App\Http\Resources\Security;

use App\Traits\HasDateFormats;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Role;

/**
 * @mixin Role
 */
class RoleResource extends JsonResource
{
    use HasDateFormats;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'guard_name' => $this->guard_name,
            'permissions_count' => $this->permissions_count ?? ($this->relationLoaded('permissions') ? $this->permissions->count() : 0),
            'users_count' => $this->users_count ?? ($this->relationLoaded('users') ? $this->users->count() : 0),
            'permissions' => $this->whenLoaded('permissions', fn () => $this->permissions->pluck('name')),
            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
        ];
    }
}
