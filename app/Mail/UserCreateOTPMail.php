<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserCreateOTPMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $email, $otp)
    {
        //
        $this->name = $name;
        $this->email = $email;
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('elesonic@gm.com')->subject('OTP Verification - Elesonic')->markdown('mails.OTPOnUserCreate', [
            'name' => $this->name,
            'email' => $this->email,
            'otp' => $this->otp,
        ]);
    }
}
