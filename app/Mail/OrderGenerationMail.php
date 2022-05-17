<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderGenerationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailFromId,$sub,$code,$fileName)
    {
        //
       
        $this->mailFromId = $mailFromId;
        $this->code = $code;
        $this->fileName = $fileName;
      
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
       
         return $this->from($this->mailFromId)->subject('Order Place E-mail')->markdown('mails.OrderEmail', [
            'orderCode' => $this->code,
        ])->attach('../public/uploads/order/'.$this->fileName, ['mime' => 'application/pdf']);
    }
}
