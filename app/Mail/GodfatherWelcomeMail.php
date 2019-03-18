<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class GodfatherWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $authInfo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($authInfo)
    {
        $this->authInfo = $authInfo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Bienvenido a Proyecto Sigue')
                    ->markdown('Email.godfather_welcome')->with([
                        'auth_info' => $this->authInfo,
                    ]);
    }
}
