<?php

namespace Backend\Root\User\Controllers;

use App\User;
use Mail;
use Request;

class UserController extends \Backend\Root\Form\Services\ResourceController
{
    function __construct(User $post)
    {
        parent::init($post);
    }

    public function create (){
        $this->fields['fields']['password']['value'] = str_random(8);
        return parent::create();
    }

    public function store()
    {
        $this->saveFields($this->post, $this->fields['fields']);

        $user = &$this->post;
        if(Request::input('send_mail', false) == 'yes'){
            Mail::send('User::emails.register-admin', [ 'user' => $user ], function($message) use ($user)
            {
                $message->to( $user['email'], '')->subject('Новый аккаунт на сайте '.url('/'));
            });
        }

        $this->post->password = bcrypt($this->post->password);
        $this->post->save();

        return [ 'redirect' =>  action($this->config['controllerName'].'@edit', $this->post->id) ];
    }

    public function edit($id)
    {
        $this->post = $this->post->findOrFail($id);
        $this->post->password = '';
        unset($this->fields['fields']['send_mail']);
        return parent::edit($id);
    }

    public function update($id)
    {
        $this->post = $this->post->findOrFail( $id );

        $this->fields['fields']['email']['validate'] .= ",".$id;
        
        if( Request::input('password', '') == '' ){
            unset($this->fields['fields']['password']);
        }
        
        $this->SaveFields($this->post, $this->fields['fields']);

        if( Request::input('password', '') != '' ) {
            $this->post->password = bcrypt($this->post->password);
        }

        $this->post->save();
    }
}