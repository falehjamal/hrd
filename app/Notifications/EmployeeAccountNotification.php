<?php

namespace App\Notifications;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeeAccountNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Employee $employee,
        public string $action,
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

        if (tenant_whatsapp_is_configured() && filled($this->employee->phone)) {
            $channels[] = 'whatsapp';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        apply_tenant_mail_config();

        $message = (new MailMessage)
            ->subject('Akun Login '.tenant_app_name())
            ->greeting('Halo '.$this->employee->name.',')
            ->line($this->actionLine())
            ->line('Username: '.$notifiable->username);

        if ($this->plainPassword !== null) {
            $message->line('Password: '.$this->plainPassword);
        }

        return $message
            ->action('Login', url(route('login')))
            ->line($this->action === 'created'
                ? 'Segera ganti password setelah login pertama.'
                : 'Jika Anda tidak merubah password, gunakan password sebelumnya.');
    }

    /**
     * @return array{phone: string, message: string}
     */
    public function toWhatsapp(object $notifiable): array
    {
        $loginUrl = url(route('login'));
        $lines = [
            "Halo {$this->employee->name},",
            '',
            $this->actionLine(),
            '',
            "Username: {$notifiable->username}",
        ];

        if ($this->plainPassword !== null) {
            $lines[] = "Password: {$this->plainPassword}";
            $lines[] = '';
        }

        $lines[] = "Login: {$loginUrl}";
        $lines[] = '';

        $lines[] = $this->action === 'created'
            ? 'Segera ganti password setelah login pertama.'
            : 'Jika Anda tidak merubah password, gunakan password sebelumnya.';

        return [
            'phone' => (string) $this->employee->phone,
            'message' => implode("\n", $lines),
        ];
    }

    private function actionLine(): string
    {
        return $this->action === 'created'
            ? 'Akun login Anda di '.tenant_app_name().' telah dibuat.'
            : 'Akun login Anda di '.tenant_app_name().' telah diperbarui.';
    }
}
