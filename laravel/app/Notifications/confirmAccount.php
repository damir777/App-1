<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\User;

class confirmAccount extends Notification
{
    use Queueable;

    private $admin;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $admin)
    {
        $this->admin = $admin;
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
        return (new MailMessage)
            ->subject('Potvrda korisničkog računa')
            ->greeting('Poštovani,')
            ->line('zahvaljujemo se na registraciji na xx. Da bi potvrdili svoj korisnički račun, molimo kliknite na 
                Potvrdi račun.')
            ->action('Potvrdi račun', url(config('app.url').'/auth/confirm/'.$this->admin->remember_token))
            ->line('Nakon potvrde korisničkog računa, imate pravo 30 dana besplatno korisniti xx. Istekom tog perioda
                korištenje se naplaćuje 50 kn mjesečno po korisniku.')
            ->line('Za sva pitanja, pomoć pri korištenju aplikacije ili vlastite sugestije, slobodno nam se javite.')
            ->salutation('S poštovanjem,<br>xx');
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
