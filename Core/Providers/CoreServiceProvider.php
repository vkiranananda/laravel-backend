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
            return new \Backend\Root\MediaFile\Services\UploadedFiles();
        });

        AliasLoader::getInstance()->alias('GetConfig', '\Backend\Root\Core\Facades\GetConfigFacade');
        AliasLoader::getInstance()->alias('Backend', '\Backend\Root\Core\Facades\BackendFacade');
        AliasLoader::getInstance()->alias('Widget', '\Backend\Root\Widget\Services\Widget');
        AliasLoader::getInstance()->alias('Helpers', '\Backend\Root\Core\Services\Helpers');
    }
}
