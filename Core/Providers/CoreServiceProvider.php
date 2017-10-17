<?php

namespace Backend\Root\Core\Providers;

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
            return new \Backend\Root\Core\Services\Backend();
        });

        $this->app->singleton('UploadedFiles', function($app)
        {
            return new \Backend\Root\Upload\Services\UploadedFiles();
        });
<<<<<<< HEAD
=======

        AliasLoader::getInstance()->alias('BackendConfig', '\Backend\Root\Core\Facades\ConfigFacade');
        AliasLoader::getInstance()->alias('Backend', '\Backend\Root\Core\Facades\BackendFacade');
        AliasLoader::getInstance()->alias('UploadedFiles', '\Backend\Root\Upload\Facades\UploadedFilesFacade');
        AliasLoader::getInstance()->alias('Categories', '\Backend\Root\Category\Facades\CategoriesFacade');
        AliasLoader::getInstance()->alias('Content', '\Backend\Root\Site\Services\Content');
        AliasLoader::getInstance()->alias('Helpers', '\Backend\Root\Core\Services\Helpers');
        AliasLoader::getInstance()->alias('Forms', '\Backend\Root\Form\Services\Forms');

>>>>>>> 335b97e178203b3721db194e913a3e19b7c70ee0
    }
}