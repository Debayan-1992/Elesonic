<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnlyTextMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $mailFromId, $txt, $subject)
    {
        //
        $this->name = $name;
        $this->mailFromId = $mailFromId;
        $this->txt = $txt;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->mailFromId)->subject($this->subject)->markdown('mails.OnlyText', [
            'name' => $this->name,
            'text' => $this->txt,
        ]);
    }
}
