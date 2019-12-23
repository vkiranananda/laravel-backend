<?php

namespace Backend\Root\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use File;
use Cache;

class ModuleCreate extends Command
{

    private $backendPath = null;
    private $templatePath = __DIR__ . "/../../../install/templates/";
    private $migrationsPath = null;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backend:module';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавить новый модуль';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->backendPath = $this->backendPath ?? base_path('backend') . '/';
        $this->migrationsPath = $this->migrationsPath ?? database_path('migrations/');

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        do {
            $module = $this->askModule('Название модуля', $module ?? null);

            $category = $this->confirm('Используем категории?', $category ?? true);

            if ($category) $app = $this->confirm('Установить контроллер в app/Http/Controllers ?', $app ?? true);

            $upload = $this->confirm('Разрешить загрузку файлов?', $upload ?? true);
            $sort = $this->confirm('Разрешить ручную сортировку данных?', $sort ?? false);
            $url = $this->confirm('Добавить поле URL?', $url ?? true);
            $seo = $this->confirm('Добавить СЕО поля?', $seo ?? true);
            $sitemap = $this->confirm('Добавить карту сайта?', $sitemap ?? true);


            $this->info("\nМодуль: " . $module);

            if ($category) {
                $this->info("Используем категории");
                if ($app) $this->info("Установаем контроллер в app/Http/Controllers");
            } 

            if ($upload) $this->info("Разрешаем загрузку файлов");
            if ($sort) $this->info("Разрешаем ручную сортировку данных");
            if ($url) $this->info("Добавляем поле URL");
            if ($seo) $this->info("Добавляем СЕО поля");
            if ($sitemap) $this->info("Добавляем карту сайта");
            

        } while(!$this->confirm('Создаем модуль?', true));

        $modPath = $this->backendPath . $module . '/';

        if ($category) {
            $this->templatePath .= 'BaseModWithCategory/';
            $this->createCategoryData(compact('modPath', 'module', 'app'));
        } else {
            $this->templatePath .= 'BaseMod/';
        }
        
        // Создаем каталог модуля
        File::makeDirectory($modPath);

        $this->createConfigs(compact('modPath', 'upload', 'sort', 'seo', 'url'));
        $this->createControllers(compact('modPath', 'module', 'upload', 'sitemap', 'sort'));
        $this->createModel($modPath, $module);
        $this->createMigrations(compact('modPath', 'module', 'sort', 'url'));
        // Чистим кэш бэкенда
        Cache::forget('backendCoreData');

        $this->info("\nМодуль: создан в " . $modPath . "\n");
       
    }


    // Конфигурим картегории и добавляем данные контроллер в app если нужно
    protected function createCategoryData($vars)
    {
        extract($vars);
        
        // Устанавливаем контроллер в app
        if ($app) {

            $fileSave = app_path("Http/Controllers/" . $module . "Controller.php");

            if (File::exists($fileSave)) {
                $this->warn("Контроллер $fileSave уже существует\n");
            } else {
            
                $this->info("Контроллер добавлен: $fileSave\n");

                $this->replaceModName($this->templatePath . "Controllers/AppController.php", $fileSave, $module);
            }
        }

        // Добавляем конфиг в категории
        $modulesConfig = $this->backendPath . 'Category/Configs/modules.php';
        $catMods = include($modulesConfig);

        // Если данного модуля тут еще нет, добавляем.
        if (!isset($catMods[$module])) {
            $catMods[$module] = [
                'resourceController' => '\Backend\\' . $module . '\Controllers\\' . $module . 'Controller',
                'viewControllerAction' => '\App\Http\Controllers\\' . $module . 'Controller',
                'categoryController' => '\App\Http\Controllers\\' . $module . 'Controller',
                'label' => $module,
            ];
            // Сохраняем файл
            $this->saveArray($modulesConfig, $catMods);
        }
    }

    // Создаем миграции
    protected function createMigrations($vars)
    {
        extract($vars);
        // Класс для файла миграций
        $className = Str::plural($module);
        // Название таблицы в бд
        $table = Str::snake($className);
        // Имя файла для сохранения
        $fileName = '2019_12_23_000000_create_' . $table . '_table.php';
        // Куда сохраняем
        $fileSave = $this->migrationsPath . $fileName;

        // Если миграция уже есть сообщаем об этом и выходим.
        if (File::exists($fileSave)) {
            $this->warn("Миграция $fileSave уже существует");
            return;
        }
        
        $data = file_get_contents($this->templatePath . 'database/migrations/create_base_mod_table.php');
        
        // Подменяем название модуля и таблиц
        $data = str_replace(['BaseMod', 'base_mod'], [$className, $table], $data);

        // Убираем лишние ячейки из миграции
        $replace = [];
        if (!$sort) $replace[] = 'sort';
        if (!$url) $replace[] = 'url';

        $data = preg_replace($this->gerRemoveRegex($replace), '', $data);

        $this->info("Миграция создана: $fileSave");
        $this->info("Измените ее как нужно и выполните команду: php artisan migrate");

        file_put_contents($fileSave, $data);
    }

    // Создаем классы
    protected function createControllers($conf)
    {
        extract($conf);

        $conPath = $modPath . 'Controllers/';
        $templatePath = $this->templatePath . 'Controllers/';

        File::makeDirectory($conPath);

        $this->replaceModName(
                $templatePath . 'PageController.php', 
                $conPath . $module . 'Controller.php', 
                $module
        );

        $routeArr = [];
        if ($upload) $routeArr[] = "'upload'";
        if ($sort) $routeArr[] = "'sortable'";
        
        $routeStr = "Backend::installRoutes('".$module."', [".implode(',', $routeArr)."]);";

        $this->info("Добавьте строку роутинга '$routeStr' в файл " . $this->backendPath . "routes.php\n");
            

        if ($upload) {
            $this->replaceModName(
                    $templatePath . 'UploadController.php', 
                    $conPath . 'UploadController.php', 
                    $module
            );
        }

        if ($sitemap) {
            $this->replaceModName(
                    $templatePath . 'SitemapController.php', 
                    $conPath . 'SitemapController.php', 
                    $module
            );
            $this->info("Настройте параметры выборки для карты сайта в " . $conPath . "SitemapController.php");
            $this->info("И добавье его в конфиг " . $this->backendPath . "Sitemap/Configs/modules.php\n");
        }
    }

    // Создаем модель
    protected function createModel($modPath, $module)
    {
        $modPath = $modPath . 'Models/';

        File::makeDirectory($modPath);
        
        $this->replaceModName(
                $this->templatePath . 'Models/BaseMod.php', 
                $modPath . $module.'.php', 
                $module
        );
    }
    
    // Заменяет название модуля в файле
    protected function replaceModName($from, $saveTo, $modName)
    {
        file_put_contents($saveTo, str_replace('BaseMod', $modName, file_get_contents ($from)));
    }

    // Создаем конфиг
    protected function createConfigs($conf)
    {
        extract($conf);

        $confPath = $modPath . 'Configs/';
        $templatePath = $this->templatePath . 'Configs/';

        File::makeDirectory($confPath);

        // Конфиг
        $replace = [];
        if (!$sort) $replace[] = 'sort';
        if (!$upload) $replace[] = 'upload';

        $this->removeStr($templatePath . 'config.php', $confPath . 'config.php', $replace);

        // Поля
        $replace = [];
        if (!$url)  $replace[] = 'url';
        if (!$seo)  $replace[] = 'seo';
        if (!$sort)  $replace[] = 'sort';
        if (!$seo && !$url)  $replace[] = 'urlseo';

        $this->removeStr($templatePath . 'fields.php', $confPath . 'fields.php', $replace);

        // Копируем конфиг для загрузок
        if ($upload) File::copy($this->templatePath . 'Configs/upload.php', $confPath . 'upload.php');
    }

    // Открываем файл, сохраняем файл, массив замен 
    protected function removeStr($from, $saveTo, $arr)
    {
        $data = file_get_contents($from);
        
        file_put_contents($saveTo, preg_replace($this->gerRemoveRegex($arr), '', $data));
    }

    // Генерирует из обычного массива, массив с регекпспами на замену
    protected function gerRemoveRegex($arr)
    {
        foreach ($arr as &$value) {
            $value = "/.*?{".$value."}.*?\n/";
        }
        //  Заменяем остатки
        $arr[] = '/{.+?}/';

        return $arr;
    }
// deleteDirectory

    protected function saveArray($file, $arr)
    {
        file_put_contents($file, "<?php\nreturn " . var_export($arr, true) . ';');
    }

    // Запрашиваем и проверяем название модуля
    protected function askModule($message, $module)
    {
        do {
            $module = $this->ask($message, $module);
        } while (!$this->checkModuleNameIsValid($module) || !$this->checkModuleExist($module));

        return $module;
    }

    // Проверяет имя модуля
    protected function checkModuleNameIsValid($module)
    {
        if (strlen($module) < 1) {
            $this->error('Укажите название модуля');
            return false;
        }

        if (!preg_match('/^[a-z0-9]+$/i', $module)) {
            $this->error('Название модуля может содержать только алфавитно цивровые символы');
            return false;
        }
        return true;
    }

    // Проверяет существование модуля
    protected function checkModuleExist($module)
    {
        if (File::exists($this->backendPath . $module)) {
            $this->error('Модуль с таким имененм уже существует');
            return false;            
        }
        return true;
    }

}