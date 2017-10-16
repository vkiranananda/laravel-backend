<?php

namespace Backend\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Backend;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Backend::init();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Backend', function($app)
        {
            return new \Backend\Core\Services\Backend();
        });

        $this->app->singleton('UploadedFiles', function($app)
        {
            return new \Backend\Upload\Services\UploadedFiles();
        });

        AliasLoader::getInstance()->alias('BackendConfig', '\Backend\Core\Facades\ConfigFacade');
        AliasLoader::getInstance()->alias('Backend', '\Backend\Core\Facades\BackendFacade');
        AliasLoader::getInstance()->alias('UploadedFiles', '\Backend\Upload\Facades\UploadedFilesFacade');
        AliasLoader::getInstance()->alias('Categories', '\Backend\Category\Facades\CategoriesFacade');
        AliasLoader::getInstance()->alias('Content', '\Backend\Site\Services\Content');
        AliasLoader::getInstance()->alias('Helpers', '\Backend\Core\Services\Helpers');
        AliasLoader::getInstance()->alias('Forms', '\Backend\Form\Services\Forms');

    }
}