<?php

namespace Backend\Root\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backend:user';

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
        $password = $this->askPassword('Пароль', 'Подтверждение пароля');

        $user = User::forceCreate(['name' => $name, 'email' => $email, 'password' => Hash::make($password)]);

       	$this->info("Пользователь создан\n");
    }


    // Запрашиваем пароль
    protected function askPassword($message, $message2)
    {
        do {
            $password = $this->secret($message);

            if (!$this->checkPasswordValid($password)) continue;

            $password2 = $this->secret($message2);

            if ($this->checkPasswordConfurm($password, $password2)) return $password;

        } while (true);
    }

    // Проверяем валидность пароля
    protected function checkPasswordValid($password)
    {
        if (strlen($password) < 6) {
        	$this->error('Пароль должен быть не менее 6 символов');
        	return false;
        }

        return true;
    }

    // Проверяем совпадение паролей
    protected function checkPasswordConfurm($password, $password2)
    {
        if ($password != $password2) {
        	$this->error('Пароли не совпадают');
        	return false;
        }

        return true;
    }

    // Запрашиваем уникальный емаил
    protected function askUniqueEmail($message)
    {
        do {
            $email = $this->ask($message, null);
        } while (!$this->checkEmailIsValid($email) || !$this->checkEmailIsUnique($email));

        return $email;
    }

	// Проверяем валидность емаила
    protected function checkEmailIsValid($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Не корректный email адрес "' . $email . '"');
            return false;
        }

        return true;
    }

    // Проверяем уникальность емаил
    public function checkEmailIsUnique($email)
    {
        if (User::whereEmail($email)->exists()) {
            $this->error('Пользователь с адресом "'.$email.'" уже существует');
            return false;
        }

        return true;
    }
}
