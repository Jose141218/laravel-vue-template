<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property Carbon|string|null $invitation_created_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, HasUuids, Notifiable, PasskeyAuthenticatable, SoftDeletes, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Scope a query to include the created_at timestamp of the pending invitation token.
     *
     * @param  Builder<User>  $query
     */
    public function scopeWithInvitationCreatedAt(Builder $query): void
    {
        $query->select('users.*')
            ->addSelect([
                'invitation_created_at' => DB::table('password_reset_tokens')
                    ->select('created_at')
                    ->whereColumn('password_reset_tokens.email', 'users.email')
                    ->limit(1),
            ]);
    }

    public function getInvitationStatus(): string
    {
        if ($this->email_verified_at !== null) {
            return 'active';
        }

        $createdAt = $this->getAttribute('invitation_created_at')
            ?? DB::table('password_reset_tokens')->where('email', $this->email)->value('created_at');

        if (! $createdAt) {
            return 'expired';
        }

        $expiresAt = Carbon::parse($createdAt)->addMinutes((int) config('auth.passwords.users.expire', 1440));

        return $expiresAt->isPast() ? 'expired' : 'pending';
    }
}
