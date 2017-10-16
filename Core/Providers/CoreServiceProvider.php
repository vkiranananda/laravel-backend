<?php

namespace Backend\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use View;
use Cache;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $path = __DIR__.'/../../';

        $viewDirs = Cache::tags('backend')->remember('view-dirs', 43200, function() use ($path)
        {
            $dir = opendir($path);
            $res = [];

            while(false !==  ($file = readdir($dir)) ) {
               if ( $file != '.' && $file != '..' && is_dir($path.$file) && is_dir($path.$file.'/resources/views') ) {
                    $res[$file] = $path.$file.'/resources/views';
               }
            }
            closedir($dir);

            return $res;
        }); 
        
        foreach ($viewDirs as $file => $tPath) {
            View::addNamespace($file,$tPath);
        }

        View::addNamespace('Backend', $path.'resources/views/');
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


        //$this->app->alias(\Backend\Upload\Services\UploadedFiles::class, 'UploadedFiles');
        AliasLoader::getInstance()->alias('BackendConfig', '\Backend\Core\Facades\ConfigFacade');
        AliasLoader::getInstance()->alias('UploadedFiles', '\Backend\Upload\Facades\UploadedFilesFacade');
        AliasLoader::getInstance()->alias('Categories', '\Backend\Category\Facades\CategoriesFacade');
        AliasLoader::getInstance()->alias('Content', '\Backend\Site\Services\Content');
        AliasLoader::getInstance()->alias('Helpers', '\Backend\Core\Services\Helpers');
        AliasLoader::getInstance()->alias('Forms', '\Backend\Form\Services\Forms');
       // $this->app->alias('MediaFiles', '\Backend\Upload\Faceds\MediaFilesFacade');
      //  $loader = \Illuminate\Foundation\AliasLoader::getInstance();
    //    $loader->alias('UploadedFiles', '\Backend\\Upload\\Services\\UploadedFiles');
    }
}
