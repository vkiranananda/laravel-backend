<?php
namespace Backend\Core\Services;
use Cache;

class Config {
    private $loadedConfigs = [];

    //Получаем конфиг
    //Если $backend == true и не указан модууль Post:: будет искать файл в бэкенде /Configs
    //Иначе в каталоге App/Configs
    public function &get($conf, $backend = true)
    {
        //Если конфиг уже загружен возвращаем его
        if(isset($this->loadedConfigs[$conf]))return $this->loadedConfigs[$conf];

        $this->loadedConfigs[$conf] = Cache::tags('configs')->remember($conf, 43200, function() use ($conf, $backend)
        {
            $config = str_replace('.', '/', $conf);

            if(strrpos($config, '::') === false){
                if($backend) $file = base_path()."/backend/Configs/".$config.".php";
                else $file = app_path().'/Configs/'.$config.".php";
            }else {
                $config = str_replace('::', '/Configs/', $config);
                $file = base_path()."/backend/".$config.".php";
            }

            return (file_exists($file)) ? include ($file) : [];

        }); 

        return $this->loadedConfigs[$conf];
    }
}