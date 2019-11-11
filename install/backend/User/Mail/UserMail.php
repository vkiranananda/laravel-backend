<?php

namespace Backend\User\Mail;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
    
    public function build()
    {
	$this->subject( 'Новый аккаунт на сайте '.url('/') );

        return $this->view('User::emails.register-admin')
                    ->with([ 'user' => $this->user ]);
    }
}