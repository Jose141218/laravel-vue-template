<?php

namespace App\Http\Resources\Security;

use App\Models\User;
use App\Traits\HasDateFormats;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
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
            'email' => $this->email,
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->map(fn (Model $role) => [
                'id' => $role->getAttribute('id'),
                'name' => $role->getAttribute('name'),
                'description' => $role->getAttribute('description'),
            ])),
            'email_verified_at' => $this->formatDate($this->email_verified_at),
            'status' => $this->getInvitationStatus(),
            'is_active' => $this->email_verified_at !== null,
            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
        ];
    }
}
