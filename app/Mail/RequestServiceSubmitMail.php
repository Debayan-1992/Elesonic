<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RequestServiceSubmitMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $mailFromId, $price, $message, $payment_link)
    {
        //
        $this->name = $name;
        $this->mailFromId = $mailFromId;
        $this->price = $price;
        $this->message = $message;
        $this->payment_link = $payment_link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->mailFromId)->subject('Service Payment')->markdown('mails.RequestServiceSubmit', [
            'name' => $this->name,
            'price' => $this->price,
            'message' => $this->message,
            'payment_link' => $this->payment_link
        ]);
    }
}
