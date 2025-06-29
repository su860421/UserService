<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword implements ShouldQueue
{
    use Queueable;

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('重設您的密碼')
            ->greeting('您好 ' . $notifiable->name . '！')
            ->line('您收到此郵件是因為我們收到了您帳戶的密碼重設請求。')
            ->action('重設密碼', $url)
            ->line('此密碼重設連結將在 ' . config('auth.passwords.users.expire', 60) . ' 分鐘後過期。')
            ->line('如果您沒有請求重設密碼，請忽略此郵件。')
            ->line('如果您沒有請求重設密碼，則無需進一步操作。');
    }
}
