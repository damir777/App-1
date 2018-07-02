<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Barryvdh\DomPDF\Facade as PDF;

class sendxxInvoice extends Notification
{
    use Queueable;

    private $pdf_data;
    private $invoice_id;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct($pdf_data, $invoice_id)
    {
        $this->pdf_data = $pdf_data;
        $this->invoice_id = $invoice_id;
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
        $pdf = PDF::loadView('app.invoices.pdf', ['data' => $this->pdf_data])->stream();

        return (new MailMessage)
            ->subject('Račun za xx')
            ->greeting('Poštovani,')
            ->line('u prilogu Vam šaljemo račun za xx.')
            ->salutation('S poštovanjem,<br>xx tim')
            ->attachData($pdf, 'xx - '.$this->invoice_id.'.pdf', [
                'mime' => 'application/pdf',
            ]);
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
