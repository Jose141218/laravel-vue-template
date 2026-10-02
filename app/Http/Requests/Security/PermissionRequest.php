<?php

namespace App\Http\Requests\Security;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class PermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $permission = $this->route('permission');
        $permissionId = $permission instanceof Permission ? $permission->id : (is_numeric($permission) || is_string($permission) ? $permission : null);

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('permissions', 'name')->where('guard_name', 'web')->ignore($permissionId),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'module_key' => ['nullable', 'string', 'max:50', 'exists:modules,key'],
        ];
    }
}
