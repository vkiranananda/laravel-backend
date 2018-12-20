<?php

namespace Backend\Root\User\Controllers;

use App\User;
use Mail;
use Request;
use Log;

class UserController extends \Backend\Root\Form\Controllers\ResourceController
{
    function __construct(User $post)
    {
        parent::init($post);
    }

    //Криптуем пароль и отправляем email
    protected function preSaveData($type){ 
    	
    	$fields = Request::input('fields', []);

    	if ($type == 'store') {
	        if ( isset($fields['send_mail']) && $fields['send_mail'] == 'yes' ) {

	        	Mail::to($this->user['email'])
	        		->subject( 'Новый аккаунт на сайте '.url('/') )
	        		->send(new \Backend\Root\User\Mail\UserMail($this->post));

	        }
    	}

    	// Криптуем пароль
    	if ($fields['password'] != '') $this->post['password'] = bcrypt($fields['password']);
    }

	protected function resourceCombine($type){ 

    	// Генерим пароль по умолчанию при создании записи
		if ($type == 'create') {
        	$this->fields['fields']['password']['value'] = str_random(8);
		}
		// добавляем валидацию
		elseif ($type == 'store') {
        	$this->fields['fields']['password']['validate'] .= "|required";
		}
		// Убираем поле отправки на email. И очищаем хэш пароля
		elseif ($type == 'edit') { 
			unset($this->fields['fields']['send_mail']);
			$this->post->password = '';
		} 

		// При обновлении записи
		elseif ($type == 'update') {
			// Для unique валидации добавляем id в исключение
        	$this->fields['fields']['email']['validate'] .= ",".$this->post->id;
	
			// Если пароль не был задан, оставляем тот что бы ранее
	        if ( Request::input('fields')['password'] == '' ) {
	            unset($this->fields['fields']['password']);
	        }
		}
	}
}