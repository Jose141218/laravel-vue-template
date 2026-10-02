<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $token
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
            'invitation' => 1,
        ]);

        $expireHours = (int) (config('auth.passwords.users.expire', 1440) / 60);

        return (new MailMessage)
            ->subject('Invitation to join '.config('app.name'))
            ->greeting('Hello, '.$notifiable->name.'!')
            ->line('You have been invited to access '.config('app.name').'.')
            ->line('To activate your account and set up your password, please click the button below:')
            ->action('Activate Account & Set Password', $url)
            ->line("This invitation link will expire in {$expireHours} hours.")
            ->line('If you were not expecting this invitation, no further action is required.');
    }
}
