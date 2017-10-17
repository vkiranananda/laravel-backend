<?php

namespace Backend\Root\User\Controllers;

use App\User;
use Mail;
use Request;

class UserController extends \Backend\Root\Form\Services\ResourceController
{
    function __construct(User $post)
    {
        parent::init($post, 'User::users');
    }

    public function create (){
        $this->params['fields']['password']['value'] = str_random(8);
        return parent::create();
    }

    public function store()
    {
        $this->saveFields($this->post, $this->params['fields']);

        $user = &$this->post;
        if(Request::input('send_mail', false) == 'yes'){
            Mail::send('User::emails.register-admin', [ 'user' => $user ], function($message) use ($user)
            {
                $message->to( $user['email'], '')->subject('Новый аккаунт на сайте '.url('/'));
            });
        }

        $this->post->password = bcrypt($this->post->password);
        $this->post->save();

        return [ 'redirect' =>  action($this->params['controllerName'].'@edit', $this->post->id) ];
    }

    public function edit($id)
    {
        $this->post = $this->post->findOrFail($id);
        $this->post->password = '';
        unset($this->params['fields']['send_mail']);
        return parent::edit($id);
    }

    public function update($id)
    {
        $this->post = $this->post->findOrFail( $id );

        $this->params['fields']['email']['validate'] .= ",".$id;
        
        if( ! Request::has('password') ){
            unset($this->params['fields']['password']);
        }

        $this->SaveFields($this->post, $this->params['edit']);

        if( Request::has('password') ) {
            $this->post->password = bcrypt($this->post->password);
        }

        $this->post->save();
    }
}