<?php

namespace Backend\Root\User\Controllers\Mail;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderShipped extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
    
    public function build()
    {
        return $this->view('User::emails.register-admin')
                    ->with([ 'user' => $this->user ]);
    }
}