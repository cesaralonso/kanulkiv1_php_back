<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use App\Tenant;

class CustomResetPasswordNotification extends ResetPassword
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token=$token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
   public function toMail($notifiable)
    {
        $tenant = Tenant::where('user_id',$notifiable->id)->first();
        $url = route('password.reset').'?token='.$this->token.'&email='.$notifiable->email;
        return (new MailMessage)
            ->from("no-reply@kanulki.com")
            ->subject('Reestablecer Contraseña Kanulki')
            ->greeting('Hola '.$tenant->name.",")
            ->line('Recibimos tu solicitud para reestablecer tu contraseña, para continuar da click en "Reestablecer Contraseña"')
            ->action('Reestablecer Contraseña', $url)
            ->line('Si tu no hiciste esta solicitud, ignora este mensaje')
            ->salutation('Saludos, '. config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
