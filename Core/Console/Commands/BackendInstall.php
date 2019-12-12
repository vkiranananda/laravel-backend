<?php

namespace Backend\Root\Core\Console\Commands;

use Illuminate\Console\Command;
use File;

class BackendInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backend:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Установка пользовательской части админки';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	$backendName = 'backend';
    	$installPath = __DIR__ . "/../../../install/";
    	$backendPath = base_path($backendName);
    	$migrationsPath = database_path('migrations/');
    	$publicBackend = public_path($backendName);

    	if (File::exists($backendPath)) {
    		echo "Бэкенд уже установлен.\n";
    	} else {
    		// Копируем каталог с модулями
    		File::copyDirectory($installPath . 'backend', $backendPath);

    		// Инсталим миграции
    		foreach (File::files($installPath . 'migrations') as  $file) {
    			$fileName = basename($file);
    		
    			if (File::exists($migrationsPath . $fileName)) {
    				echo "Миграция $fileName уже существует, пропускаю.\n";
    			} else {
    				File::copy($file ,$migrationsPath . $fileName);
    			}
    		}

    		if (File::exists($publicBackend)) {
    			echo "Каталог $publicBackend уже сущетвует, пропускаю.\n";
    		} else {
    			File::copyDirectory($installPath . 'public/backend', $publicBackend);
    		}
    	}

    	echo "\nДальнейшие инструкции\n\n";

    	echo "Добавляем в файл с маршрутами routes/web.php строку\n";
    	echo "Backend::installRoutes('Backend');\n\n";
    	echo "Добавляем в файл composer.json в секцию autoload -> psr-4 новое пространство имен\n";
    	echo "\"Backend\\\": \"backend/\"\n\n";
    	echo "Все миграции были скопированы в $migrationsPath, наберите \n";
    	echo "php artisan migrate\n";

    	echo "\nФАЙЛОВАЯ СТРУКТУРА БЭКЕНДА КЭШИРУЕТСЯ, ЕСЛИ ВЫ СДЕЛАЛИ ТАМ КАКИЕ ТО ИЗМЕНЕНИЯ, ОБЯЗАТЕЛЬНО ОЧИСТИТЕ КЭШ\n";
    	echo "php artisan cache:clear\n\n";

    	echo "Каталог с модулями $backendPath\n\n";

    	echo "Удачной работы :)\n\n";
    }
}
