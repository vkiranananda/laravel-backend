<?php

namespace Backend\Root\Core\Services;

use Cache;
use GetConfig;
use Route;
use View;

class Backend
{

    private $data;

    public function init()
    {
        // Проверка на установленный бэкенд
        $config = GetConfig::backend('backend');
        if (count($config) == 0) {
            echo "Бэкенд не установлен\n\n";
            return;
        }
        foreach ($config['views'] as $view) {
            $path = (isset($view['root']) && $view['root'] === true)
                ? base_path('vendor/vkiranananda/backend/') : base_path('backend/');

            View::addNamespace($view['name'], $path . $view['name'] . '/resources/views');
        }
        // неймспейс для бэкенда
        View::addNamespace('Backend', base_path('backend/resources/views'));
    }

    /**
     * @param string $mod
     * @param array $ext upload роутинги загрузки файлов, sortable для сортировки данных,
     * module инсталит роутинг из модуля и отменяет установку resource маршрутов.
     * Если нужно так же resource роутинг то добавляем эту опцию. По умолчанию включена если нет параметра module
     */
    public function installRoutes($mod = '', $ext = [])
    {

        if (!is_array($ext)) abort(418, 'installRoutes: Параметр $ext должен быть массивом');

        $modUrl = mb_strtolower($mod);

        $resource = true;
        foreach ($ext as $key) {
            switch ($key) {
                case 'upload':
                    $this->installUploadRoute($modUrl, $mod);
                    break;
                case 'sortable':
                    $this->installSortableRoute($modUrl, $mod);
                    break;
                case 'editable':
                    $this->installEditableRoute($modUrl, $mod);
                    break;
                case 'resource':
                    // Принудильно инсталим ресурс роутинг в том случае если есть параметр module
                    $this->installResourceRoute($modUrl, $mod);
                    $resource = false;
                    break;
                case 'module':
                    require_once(base_path('backend/' . $mod . '/routes.php'));
                    // Отменяем ресурс роутинг так как подключаем роутинг модуля
                    $resource = false;
                    break;
                default:
                    break;
            }
        }
        if ($resource) $this->installResourceRoute($modUrl, $mod);
    }

    public function installResourceRoute($modUrl, $mod, $controller = false)
    {

        if (!$controller) $controller = $mod;

        Route::resource($modUrl, '\Backend\\' . $mod . '\\Controllers\\' . $controller . 'Controller');
    }

    public function installSortableRoute($modUrl, $mod, $controller = false)
    {
        if (!$controller) $controller = $mod;

        Route::get($modUrl . '/sortable', '\Backend\\' . $mod . '\Controllers\\' . $controller . 'Controller@listSortable');
        Route::put($modUrl . '/sortable', '\Backend\\' . $mod . '\Controllers\\' . $controller . 'Controller@listSortableSave');
    }

    public function installEditableRoute($modUrl, $mod, $controller = false)
    {
        if (!$controller) $controller = $mod;

        Route::get($modUrl . '/editable/{postId}/{fieldName}', '\Backend\\' . $mod . '\Controllers\\' . $controller . 'Controller@IndexEditableEdit');
        Route::put($modUrl . '/editable/{postId}/{fieldName}', '\Backend\\' . $mod . '\Controllers\\' . $controller . 'Controller@IndexEditableUpdate');
    }

    public function installUploadRoute($modUrl, $mod, $controller = false)
    {
        if (!$controller) $controller = 'Upload';

        Route::get($modUrl . '/upload/index/{id?}', '\Backend\\' . $mod . '\Controllers\\' . $controller . 'Controller@index');
        Route::post($modUrl . '/upload', '\Backend\\' . $mod . '\Controllers\\' . $controller . 'Controller@store');
        Route::delete($modUrl . '/upload/{postId}/{fileId}', '\Backend\\' . $mod . '\Controllers\\' . $controller . 'Controller@destroy');
        Route::get($modUrl . '/upload/edit/{id?}', '\Backend\\' . $mod . '\Controllers\\' . $controller . 'Controller@edit');
        Route::put($modUrl . '/upload/update/{id?}', '\Backend\\' . $mod . '\Controllers\\' . $controller . 'Controller@update');
    }

    /**
     * Устанавливаем оснонвые маршруты
     */
    public function installBaseRoutes()
    {
        require_once(base_path('backend/routes.php'));
    }
}
