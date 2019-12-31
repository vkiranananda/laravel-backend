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

    private $backendDirName = 'backend';
    private $installPath = __DIR__ . "/../../../install/";
    private $backendPath = null;
    private $migrationsPath = null;
    private $publicBackendPath = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
    	$this->backendPath = $this->backendPath ?? base_path($this->backendDirName);
    	$this->migrationsPath = $this->migrationsPath ?? database_path('migrations/');
    	$this->publicBackendPath = $this->publicBackendPath ?? public_path($this->backendDirName);

        parent::__construct();
    }

    public function printHelp()
    {
    	$this->info("Дальнейшие инструкции\n");

    	$this->info("Добавляем в файл с маршрутами routes/web.php строку");
    	$this->line("Backend::installRoutes('Backend');\n");
    	
    	$this->info("Добавляем в файл composer.json в секцию autoload -> psr-4 новое пространство имен");
    	$this->line("\"Backend\\\\\": \"backend/\"");
        $this->info("и выполните команду:");
        $this->line("composer dumpautoload\n");

    	$this->info("Все миграции были скопированы в " . $this->migrationsPath . ", наберите");
    	$this->line("php artisan migrate");

    	$this->info("\nДобавляем в ". base_path('config/filesystems.php') . " новый диск, он необходим для загрузки файлов");

		$this->line("'uploads' => [\n"
        	."\t'driver' => 'local',\n"
        	."\t'root' => public_path().'/uploads',\n"
            ."\t'visibility' => 'public',\n"
        	."],\n");


        $this->info("Сборка фронтенда:");
        $this->line("npm install jquery popper.js bootstrap trumbowyg vue-trumbowyg lodash.clonedeep lodash.size vue vuex vue-multiselect vue-the-mask vue2-datepicker vuedraggable fecha @primer/octicons octicons\n");
        $this->info("В файл webpack.mix.js добавялем строки:");
        $this->line("mix.js('vendor/vkiranananda/backend/resources/js/backend.js', 'public/backend/js/admin.js').version();"
        	."mix.sass('vendor/vkiranananda/backend/resources/sass/backend.scss', 'public/backend/css/backend.css').options({processCssUrls: false}).version();\n");
   		$this->info("Далее запускаем компиляцию:");
        $this->line("npm run production\n");

        $this->info("ФАЙЛОВАЯ СТРУКТУРА БЭКЕНДА КЭШИРУЕТСЯ, ЕСЛИ ВЫ СДЕЛАЛИ ТАМ КАКИЕ ТО ИЗМЕНЕНИЯ, ОБЯЗАТЕЛЬНО ОЧИСТИТЕ КЭШ");
        $this->line("php artisan cache:clear\n");

    	$this->info("Каталог с модулями " . $this->backendPath . "\n");

    	$this->info("Для добавления нового пользователя используйте команду:");
    	$this->line("php artisan backend:user\n");
    	
    	$this->info("URL админки ". url('content'). "\n");
    	$this->info("Удачной работы :)\n");    	
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
    	if (File::exists($this->backendPath)) {
    		$this->info("Бэкенд уже установлен.\n");
    	} else {
    		$this->info("Копирую файлы...");
    		// Копируем каталог с модулями
    		File::copyDirectory($this->installPath . 'backend', $this->backendPath);

    		// Инсталим миграции
    		foreach (File::files($this->installPath . 'migrations') as  $file) {
    			$fileName = basename($file);
    		
    			if (File::exists($this->migrationsPath . $fileName)) {
    				$this->line("Миграция $fileName уже существует, пропускаю.");
    			} else {
    				File::copy($file, $this->migrationsPath . $fileName);
    			}
    		}

    		if (File::exists($this->publicBackendPath)) {
    			$this->line("Каталог $publicBackend уже сущетвует, пропускаю.");
    		} else {
    			File::copyDirectory($this->installPath . 'public/backend', $this->publicBackendPath);
    		}

    		// Очищаем кэши
    		$this->call('cache:clear');

    		$this->info("\nБэкенд удачно установлен!\n");
    	}
    	$this->printHelp();
    }
}
