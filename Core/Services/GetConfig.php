<?php
namespace Backend\Root\Core\Services;

class GetConfig {
    private $loadedConfigs = [];

    //Получаем конфиг
    //Если $backend == true и не указан модуль Post:: будет искать файл в бэкенде /Configs
    //Иначе в каталоге App/Configs

    public function &backend($conf, $root = false)
    {
        $type = ($root) ? 'root' : 'backend' ;
        // Если конфиг уже загружен возвращаем его
        if (isset($this->loadedConfigs[$type][$conf])) return $this->loadedConfigs[$type][$conf];

        $config = str_replace('.', '/', $conf);
        $pathRoot = base_path('vendor/vkiranananda/backend/');
        $pathExt = base_path('backend/');

        $path = (strrpos($config, '::') === false) ? "Configs/".$config.".php" :  str_replace('::', '/Configs/', $config).".php";

        $this->loadedConfigs[$type][$conf] = ($root == true) ? include ($pathRoot.$path) : include ($pathExt.$path);

        return $this->loadedConfigs[$type][$conf];
    }

    public function app($conf)
    {
        if (isset($this->loadedConfigs['app'][$conf]))return $this->loadedConfigs['app'][$conf];

        $file = base_path('app/Configs/'.$conf.'.php');
        $this->loadedConfigs['app'][$conf] = (is_file($file)) ? include ($file) : [];

        return $this->loadedConfigs['app'][$conf];
    }
}