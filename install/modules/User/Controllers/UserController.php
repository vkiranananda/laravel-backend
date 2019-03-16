<?php

namespace Backend\User\Controllers;

use Mail;
use Request;
use Illuminate\Support\Facades\Hash;

class UserController extends \Backend\Root\Form\Controllers\ResourceController
{
	public $model = "App\User";

	public function create() 
	{
		// Генерим пароль
		$this->fields['fields']['password']['value'] = str_random(8);

		return parent::create();
	}

    public function store() 
    {
    	// добавляем валидацию
    	$this->fields['fields']['password']['validate'] .= "|required";

    	return parent::store();
    }

    public function edit($id)
    {
    	// Убираем поле отправки на email. И очищаем хэш пароля
    	unset($this->fields['fields']['send_mail']);
		$this->post->password = '';

		return parent::edit($id);
    }

    public function update($id)
    {
		// Для unique валидации добавляем id в исключение
    	$this->fields['fields']['email']['validate'] .= ",".$this->post->id;

		// Если пароль не был задан, оставляем тот что бы ранее
        if ( Request::input('fields')['password'] == '' ) {
        	unset($this->fields['fields']['password']);
        }

        return parent::update($id);
    }

    //Криптуем пароль и отправляем email
    protected function preSaveData($type){ 
    	
    	$fields = Request::input('fields', []);

    	if ($type == 'store') {
	        if ( isset($fields['send_mail']) && $fields['send_mail'] == 'yes' ) {

	        	Mail::to($this->post['email'])
	        		->send(new \Backend\User\Mail\UserMail($this->post));

	        }
    	}

    	// Криптуем пароль
    	if ($fields['password'] != '') $this->post['password'] = Hash::make($fields['password']);
    }
}