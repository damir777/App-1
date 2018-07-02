<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Barryvdh\DomPDF\Facade as PDF;

class documentEmail extends Notification
{
    use Queueable;

    private $sender_name;
    private $document;
    private $data;
    private $is_offer;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct($sender_name, $document, $data, $is_offer)
    {
        $this->sender_name = $sender_name;
        $this->document = $document;
        $this->data = $data;
        $this->is_offer = $is_offer;
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
        if ($this->is_offer == 'T')
        {
            $pdf = PDF::loadView('app.offers.pdf', ['data' => $this->data])->stream();
            $offer_id = str_replace('/', '-', $this->document->offer_id);

            return (new MailMessage)
                ->subject(trans('main.offer').' '.$this->document->offer_id)
                ->greeting('Poštovani,')
                ->line('u prilogu Vam dostavljamo ponudu za traženu uslugu.')
                ->salutation('S poštovanjem,<br>'.$this->sender_name)
                ->attachData($pdf, 'xx - '.$offer_id.'.pdf', [
                    'mime' => 'application/pdf',
                ]);
        }
        else
        {
            $pdf = PDF::loadView('app.invoices.pdf', ['data' => $this->data])->stream();
            $invoice_id = str_replace('/', '-', $this->document->invoice_id);

            return (new MailMessage)
                ->subject(trans('main.invoice').' '.$this->document->invoice_id)
                ->greeting('Poštovani,')
                ->line('u prilogu Vam dostavljamo račun za traženu uslugu.')
                ->salutation('S poštovanjem,<br>'.$this->sender_name)
                ->attachData($pdf, 'xx - '.$invoice_id.'.pdf', [
                    'mime' => 'application/pdf',
                ]);
        }
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
