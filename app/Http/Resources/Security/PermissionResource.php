<?php

namespace App\Http\Resources\Security;

use App\Traits\HasDateFormats;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Permission;

/**
 * @mixin Permission
 *
 * @property string|null $description
 * @property string|null $module_key
 */
class PermissionResource extends JsonResource
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
            'module_key' => $this->module_key,
            'guard_name' => $this->guard_name,
            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
        ];
    }
}
