<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FrontEndPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $mailFromId, $link)
    {
        //
        $this->name = $name;
        $this->mailFromId = $mailFromId;
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->mailFromId)->subject('Password Reset - Elesonic')->markdown('mails.FrontEndPasswordReset', [
            'name' => $this->name,
            'link' => $this->link,
        ]);
    }
}
