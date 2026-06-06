<?php

namespace App\Notifications;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeeAccountCreated extends Notification
{
    use Queueable;

    public function __construct(
        public Employee $employee,
        public string $plainPassword,
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

        return (new MailMessage)
            ->subject('Akun Login '.tenant_app_name())
            ->greeting('Halo '.$this->employee->name.',')
            ->line('Akun login Anda di '.tenant_app_name().' telah dibuat.')
            ->line('Username: '.$notifiable->username)
            ->line('Password sementara: '.$this->plainPassword)
            ->action('Login', url(route('login')))
            ->line('Segera ganti password setelah login pertama.');
    }

    /**
     * @return array{phone: string, message: string}
     */
    public function toWhatsapp(object $notifiable): array
    {
        $loginUrl = url(route('login'));

        return [
            'phone' => (string) $this->employee->phone,
            'message' => "Halo {$this->employee->name},\n\n"
                .'Akun login Anda di '.tenant_app_name()." telah dibuat.\n\n"
                ."Username: {$notifiable->username}\n"
                ."Password sementara: {$this->plainPassword}\n\n"
                ."Login: {$loginUrl}\n\n"
                .'Segera ganti password setelah login pertama.',
        ];
    }
}
