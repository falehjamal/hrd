<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $type,
        public ?string $plainPassword = null,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];

        if (tenant_mail_is_configured()) {
            $channels[] = 'mail';
        }

        if (tenant_whatsapp_is_configured() && filled($notifiable->employee?->phone)) {
            $channels[] = 'whatsapp';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        apply_tenant_mail_config();

        $message = (new MailMessage)
            ->subject($this->subject())
            ->greeting('Halo '.$notifiable->name.',')
            ->line($this->actionLine())
            ->line('Username: '.$notifiable->username)
            ->line('Email: '.$notifiable->email);

        if ($this->type === 'password' && $this->plainPassword !== null) {
            $message->line('Password baru: '.$this->plainPassword);
        }

        return $message
            ->action('Login', url(route('login')))
            ->line('Jika Anda tidak melakukan perubahan ini, segera hubungi HR.');
    }

    /**
     * @return array{phone: string, message: string}
     */
    public function toWhatsapp(object $notifiable): array
    {
        $loginUrl = url(route('login'));
        $lines = [
            "Halo {$notifiable->name},",
            '',
            $this->actionLine(),
            '',
            "Username: {$notifiable->username}",
            "Email: {$notifiable->email}",
        ];

        if ($this->type === 'password' && $this->plainPassword !== null) {
            $lines[] = "Password baru: {$this->plainPassword}";
            $lines[] = '';
        }

        $lines[] = "Login: {$loginUrl}";
        $lines[] = '';
        $lines[] = 'Jika Anda tidak melakukan perubahan ini, segera hubungi HR.';

        return [
            'phone' => (string) $notifiable->employee->phone,
            'message' => implode("\n", $lines),
        ];
    }

    private function subject(): string
    {
        return $this->type === 'password'
            ? 'Password Diperbarui — '.tenant_app_name()
            : 'Profil Diperbarui — '.tenant_app_name();
    }

    private function actionLine(): string
    {
        return $this->type === 'password'
            ? 'Password akun Anda di '.tenant_app_name().' telah diperbarui.'
            : 'Profil akun Anda di '.tenant_app_name().' telah diperbarui.';
    }
}
