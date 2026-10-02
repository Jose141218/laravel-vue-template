<?php

namespace App\Http\Requests\Security;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
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
        $user = $this->route('user');
        $userId = $user instanceof User ? $user->id : (is_string($user) ? $user : null);
        $isCreate = $this->isMethod('post');
        $sendInvitation = $isCreate && $this->boolean('send_invitation');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'send_invitation' => ['nullable', 'boolean'],
            'password' => $isCreate && ! $sendInvitation
                ? ['required', 'string', Password::defaults()]
                : ['nullable', 'string', Password::defaults()],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ];
    }
}
