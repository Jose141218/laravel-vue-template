<?php

namespace App\Http\Requests\Security;

use App\Models\Module;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $module = $this->route('module');
        $moduleId = $module instanceof Module ? $module->id : (is_string($module) ? $module : null);

        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'key' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('modules', 'key')->ignore($moduleId),
            ],
        ];
    }
}
