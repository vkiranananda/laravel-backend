<?php

namespace Backend\Root\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\User;

class UserCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backend:user_create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавить нового пользователя';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $name     = $this->ask('Имя пользователя', null);
        $email    = $this->askUniqueEmail('Email адрес');
        $password = $this->askPassword('Пароль');

        if ( $this->confirm("Создаю пользователя {$name} <{$email}>?", true) ){
            $user = User::forceCreate(['name' => $name, 'email' => $email, 'password' => Hash::make($password)]);

        	$this->info("Пользователь создан");    	
        }
    }

    protected function askPassword($message)
    {
        do {
            $password = $this->secret($message, null);

            if (strlen($password) > 5) break;
            
            $this->error('Пароль должен быть не менее 6 символов');

        } while (true);

        return $password;
    }

    protected function askUniqueEmail($message)
    {
        do {
            $email = $this->ask($message, null);
        } while (!$this->checkEmailIsValid($email) || !$this->checkEmailIsUnique($email));

        return $email;
    }

	/**
	 * @param $email
	 * @return bool
	 */
    protected function checkEmailIsValid($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Не корректный email адрес "' . $email . '"');
            return false;
        }

        return true;
    }

	/**
	 * @param $email
	 * @return bool
	 */
    public function checkEmailIsUnique($email)
    {
        if (User::whereEmail($email)->first()) {
            $this->error('Пользователь с адресом "'.$email.'" уже существует');
            return false;
        }

        return true;
    }
}