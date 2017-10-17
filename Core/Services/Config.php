<?php
namespace Backend\Root\Core\Services;
use Cache;

class Config {
    private $loadedConfigs = [];

    //Получаем конфиг
    //Если $backend == true и не указан модууль Post:: будет искать файл в бэкенде /Configs
    //Иначе в каталоге App/Configs
    public function &get($conf, $backend = false)
    {
        //Если конфиг уже загружен возвращаем его
        if(isset($this->loadedConfigs[$conf]))return $this->loadedConfigs[$conf];

        $this->loadedConfigs[$conf] = Cache::tags('configs')->remember($conf, 43200, function() use ($conf, $backend)
        {
            $config = str_replace('.', '/', $conf);
            $pathMain = base_path('vendor/vkiranananda/backend/');
            $pathExt = base_path('backend/');

            $path = (strrpos($config, '::') === false) ? "Configs/".$config.".php" :  str_replace('::', '/Configs/', $config).".php";

            if($backend == true){
                return (is_file($pathMain.$path)) ? include ($pathMain.$path) : [];
            }
            if(is_file($pathExt.$path))return include ($pathExt.$path);
            if(is_file($pathMain.$path))return include ($pathMain.$path);
            return [];
        }); 

        return $this->loadedConfigs[$conf];
    }
}