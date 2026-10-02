<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Traits\PasswordValidationRules;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  array<string, string>  $input
     */
    public function reset(User $user, array $input): void
    {
        Validator::make($input, [
            'password' => $this->passwordRules(),
        ])->validate();

        $user->forceFill([
            'password' => $input['password'],
            'email_verified_at' => $user->email_verified_at ?? Carbon::now(),
        ])->save();
    }
}
