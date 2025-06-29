<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class EmailVerificationNotification extends VerifyEmail
{
    use Queueable;

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable);
        }

        $url = URL::temporarySignedRoute(
            'api.verification.verify', // 使用正確的路由名稱
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return $url;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        try {
            $verificationUrl = $this->verificationUrl($notifiable);

            $mailMessage = (new MailMessage)
                ->subject('驗證您的電子郵件地址')
                ->greeting('您好 ' . $notifiable->name . '！')
                ->line('感謝您註冊我們的服務。請點擊下方按鈕驗證您的電子郵件地址：')
                ->action('驗證電子郵件', $verificationUrl)
                ->line('如果您沒有註冊我們的服務，請忽略此郵件。')
                ->line('此驗證連結將在 ' . config('auth.verification.expire', 60) . ' 分鐘後過期。');

            return $mailMessage;
        } catch (\Exception $e) {
            Log::error('郵件內容生成失敗', [
                'user_id' => $notifiable->id ?? 'unknown',
                'email' => $notifiable->email ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
