<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\User;
use App\Company;

class newUser extends Notification
{
    use Queueable;

    private $admin;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($admin)
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
        $company = Company::find($this->admin->company_id);

        $email_text = 'Naziv tvrtke: '.$company->name.'<br>Ime i prezime: '.$this->admin->first_name.' '.
            $this->admin->last_name;

        if ($company->website)
        {
            $email_text .= '<br>Web stranica: '.$company->website;
        }

        return (new MailMessage)
            ->subject('Nova registracija na xx')
            ->greeting('Novi korisnik!')
            ->line($email_text);
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
