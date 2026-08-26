<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RestablecerContrasenaNotification extends Notification
{
    use Queueable;

    /**
     * El token de restablecimiento de contraseña.
     */
    public string $token;

    /**
     * Crear una nueva instancia de notificación.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Obtener los canales de entrega de la notificación.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Obtener la representación en correo electrónico de la notificación.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'correo' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Restablecimiento de Contraseña | Inventory System')
            ->greeting('¡Hola, ' . ($notifiable->nombre ?? 'Usuario') . '!')
            ->line('Recibiste este correo porque se solicitó un restablecimiento de contraseña para tu cuenta en Inventory System.')
            ->action('Restablecer Contraseña', $url)
            ->line('Este enlace de restablecimiento de contraseña expirará en 60 minutos.')
            ->line('Si no solicitaste este cambio, puedes ignorar este mensaje con total seguridad.')
            ->salutation('Saludos cordiales,' . "\n" . 'Equipo de Inventory System');
    }
}
