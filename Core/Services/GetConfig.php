<?php
namespace Backend\Root\Core\Services;
use Cache;

class GetConfig {
    private $loadedConfigs = [];

    //Получаем конфиг
    //Если $backend == true и не указан модууль Post:: будет искать файл в бэкенде /Configs
    //Иначе в каталоге App/Configs

    public function &backend($conf, $root = false)
    {
        $type = ($conf) ? 'root' : 'backend' ;
        //Если конфиг уже загружен возвращаем его
        if(isset($this->loadedConfigs[$type][$conf]))return $this->loadedConfigs[$type][$conf];



        $this->loadedConfigs[$type][$conf] = Cache::tags('configs')->remember($conf, 43200, function() use ($conf, $root)
        {
            $config = str_replace('.', '/', $conf);
            $pathRoot = base_path('vendor/vkiranananda/backend/');
            $pathExt = base_path('backend/');

            $path = (strrpos($config, '::') === false) ? "Configs/".$config.".php" :  str_replace('::', '/Configs/', $config).".php";

            if($root == true){
                return (is_file($pathRoot.$path)) ? include ($pathRoot.$path) : [];
            }
            if(is_file($pathExt.$path))return include ($pathExt.$path);
            if(is_file($pathRoot.$path))return include ($pathRoot.$path);
            return [];
        }); 

        return $this->loadedConfigs[$type][$conf];

    }

    public function app($conf)
    {
        if(isset($this->loadedConfigs['app'][$conf]))return $this->loadedConfigs['app'][$conf];

        $this->loadedConfigs['app'][$conf] = Cache::tags('configs')->remember("app-".$conf, 43200, function() use ($conf)
        {
            $file = base_path('app/Configs/'.$conf.'.php');

            if(is_file($file)) return include ($file);
            return [];
        }); 
        return $this->loadedConfigs['app'][$conf];
    }
}