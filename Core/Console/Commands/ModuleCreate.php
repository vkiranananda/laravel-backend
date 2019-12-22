<?php

namespace Backend\Root\Core\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use File;

class ModuleCreate extends Command
{

    private $backendPath = null;
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
        $this->backendPath = $this->backendPath ?? base_path('backend');

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

            $table = Str::plural(Str::snake($module));

            $category = $this->confirm('Используем категории?', $category ?? true);

            if ($category) $app = $this->confirm('Установить контроллер в app/Http/Controllers ?', $app ?? true);

            $upload = $this->confirm('Разрешить загрузку файлов?', $upload ?? true);
            $sitemap = $this->confirm('Добавить карту сайта?', $upload ?? true);


            $this->info("\nМодуль: " . $module);
            $this->info("Таблица БД: " . $table);

            if ($category) {
                $this->info("Используем категориями");
                if ($app) $this->info("Установаем контроллер в app/Http/Controllers");
            } 

            if ($upload) $this->info("Разрешить загрузку файлов");
            if ($sitemap) $this->info("Добавляем карту сайта");
            

        } while(!$this->confirm('Создаем модуль?', true));


        $this->info("\nМодуль: создан в " . $this->backendPath . "\n");
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
        if (File::exists($this->backendPath . '/' . $module)) {
            $this->error('Модуль с таким имененм уже существует');
            return false;            
        }
        return true;
    }

}