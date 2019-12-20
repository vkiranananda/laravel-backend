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
    	$backendName = 'backend2';
    	$installPath = __DIR__ . "/../../../install/";
    	$backendPath = base_path($backendName);
    	$migrationsPath = database_path('migrations/');
    	$publicBackend = public_path($backendName);

    	if (File::exists($backendPath)) {
    		$this->info("Бэкенд уже установлен.\n");
    	} else {
    		$this->info("Копирую файлы...");
    		// Копируем каталог с модулями
    		File::copyDirectory($installPath . 'backend', $backendPath);

    		// Инсталим миграции
    		foreach (File::files($installPath . 'migrations') as  $file) {
    			$fileName = basename($file);
    		
    			if (File::exists($migrationsPath . $fileName)) {
    				$this->line("Миграция $fileName уже существует, пропускаю.");
    			} else {
    				File::copy($file ,$migrationsPath . $fileName);
    			}
    		}

    		if (File::exists($publicBackend)) {
    			$this->line("Каталог $publicBackend уже сущетвует, пропускаю.");
    		} else {
    			File::copyDirectory($installPath . 'public/backend', $publicBackend);
    		}

    		$this->info("\nБэкенд удачно установлен!\n");
    	}


    	$this->info("Дальнейшие инструкции\n");

    	$this->info("Добавляем в файл с маршрутами routes/web.php строку");
    	$this->line("Backend::installRoutes('Backend');\n");
    	
    	$this->info("Добавляем в файл composer.json в секцию autoload -> psr-4 новое пространство имен");
    	$this->line("\"Backend\\\\\": \"backend/\"");
        $this->info("и выполните команду:");
        $this->line("composer dumpautoload\n");

    	$this->info("Все миграции были скопированы в $migrationsPath, наберите");
    	$this->line("php artisan migrate");

    	$this->info("\nДобавляем в ". base_path('config/filesystems.php') . " новый диск, он необходим для загрузки файлов");

		$this->line("'uploads' => [\n"
        	."\t'driver' => 'local',\n"
        	."\t'root' => public_path().'/uploads',\n"
            ."\t'visibility' => 'public',\n"
        	."],\n");


        $this->info("Сборка фронтенда:");
        $this->line("npm install jquery popper.js bootstrap trumbowyg vue-trumbowyg lodash.clonedeep lodash.size vue vuex vue-multiselect vue-the-mask vue2-datepicker vuedraggable fecha @primer/octicons octicons");
        $this->info("В файл webpack.mix.js добавялем строки:");
        $this->line("mix.js('vendor/vkiranananda/backend/resources/js/backend.js', 'public/backend/js/admin.js').version();"
        	."mix.sass('vendor/vkiranananda/backend/resources/sass/backend.scss', 'public/backend/css/backend.css').options({processCssUrls: false}).version();");
   		$this->info("Далее запускаем компиляцию:");
        $this->line("npm run production\n");

        $this->info("ФАЙЛОВАЯ СТРУКТУРА БЭКЕНДА КЭШИРУЕТСЯ, ЕСЛИ ВЫ СДЕЛАЛИ ТАМ КАКИЕ ТО ИЗМЕНЕНИЯ, ОБЯЗАТЕЛЬНО ОЧИСТИТЕ КЭШ");
        $this->line("php artisan cache:clear\n");

    	$this->info("Каталог с модулями $backendPath\n");

    	$this->info("Для добавления нового пользователя используйте команду:");
    	$this->line("php artisan backend:user_create\n");
    	
    	$this->info("URL админки ". url('content'). "\n");
    	$this->info("Удачной работы :)\n");

    }
}
