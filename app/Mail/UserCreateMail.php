<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\URL;

class UserCreateMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $email, $password)
    {
        //
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        //$this->verify_token = encrypt($email);
        $this->verify_url = URL::to("validate-user/?_token=".encrypt($email));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //return $this->from(config()->get('mail.username'))->subject('User creation - Elesonic')->markdown('mails.MailOnUserCreate', [
        return $this->from('elesonic@gm.com')->subject('Email Verification - Elesonic')->markdown('mails.MailOnUserCreate', [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            //'verify_token' => $this->verify_token,
            'verify_url' => $this->verify_url,
        ]);
    }
}
