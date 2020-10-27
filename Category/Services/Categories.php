<?php

namespace Backend\Root\Category\Services;

use Backend\Category\Models\Category;
use Cache;
use GetConfig;
use Helpers;
use Route;

class Categories
{
    private $allCat = [];
    private $moduleControllers = [];

    function __construct()
    {
        if (!app()->runningInConsole()) $this->init();
    }

    public function init()
    {

        $this->allCat = Cache::rememberForever('backendCategoryAll', function () {
            $allCat = [];

            foreach (Category::orderBy('sort_num')->get() as $cat) {
                $id = $cat['id'];
                //Устанавливаем основные параметры
                $allCat[$id] = Helpers::setArray($cat, ['name', 'id', 'url', 'category_id', 'mod']);

                // Для корневых категорий (Разделов)
                if ($cat['category_id'] == 0 && is_array($cat['array_data']['fields'])) {

                    foreach ($cat['array_data']['fields'] as $key => $el) {
                        // Параметры с префиксом conf-*
                        if (preg_match("/^conf-(.*)/", $key, $matches)) {
                            $allCat[$id]['conf'][$matches[1]] = $el;
                        } // Локализация
                        elseif (preg_match('/^lang-(.*)/', $key, $matches)) {
                            $allCat[$id]['lang'][$matches[1]] = $el;
                        }
                    }
                }
            }
            return $allCat;
        });

        $this->moduleControllers = GetConfig::backend('Category::modules');
    }

    // Получаем данные по типу раздела
    public function getModuleControllers($mod)
    {
        return $this->moduleControllers[$mod];
    }

    // Устанавливаем роуты, передаем либо конкретный метод, либо массив методов
    public function installRoutes($method = 'get')
    {
        if (!is_array($method)) $method = [$method];

        foreach ($method as $m) {
            Route::$m('{url}', '\Backend\Root\Category\Controllers\RouteController@index')->where('url', '.*');
        }
    }

    // Количество категорий
    public function count()
    {
        return count($this->allCat);
    }

    // Очищаем все кэши
    public function clearAllCache()
    {
        Cache::forget('backendCategoryRoutes');
        Cache::forget('backendCategoryAll');
    }

    // Выводим роуты
    public function printRoutes()
    {
        $curClass = &$this;

        return Cache::rememberForever('backendCategoryRoutes', function () use ($curClass) {
            $routes = [];
            $curClass->getRoutes($routes);
            return $routes;
        });
        dd($routes);
    }

    // Генерим все роуты, рекурсивная функция
    private function getRoutes(&$res, $category_id = 0, $url = '')
    {
        foreach ($this->allCat as $cat) {
            $newUrl = $url;
            if ($cat['category_id'] == $category_id) {
                // if ($category_id == 0) $newUrl = '';
                if ($cat['url'] != '') {
                    // Если урл не корневой  добавляем к нем разделитель.
                    if ($newUrl != '/') $newUrl .= '/';
                    $newUrl .= $cat['url'];
                }
                // Если урл не выставлен ставим как / для базовых категорий
                elseif ($category_id == 0) {
                    $newUrl = '/';
                }

                // Обрабатываем раздел с одинаковым урлом, выставляем первым первый
                // попавшийся или если выставлен параметр 'main-route' в конфиге модулей
                // То выставляем этот раздел первым.
                if ($category_id == 0 && isset($res[$newUrl])
                    && isset($this->getModuleControllers($cat['mod'])['main-route'])) {
                    $res[$newUrl] = array_merge([$cat['mod'] => [$cat['id']]], $res[$newUrl]);
                } else {
                    $res[$newUrl][$cat['mod']][] = $cat['id'];
                }
                $this->getRoutes($res, $cat['id'], $newUrl);
            }
        }
    }


    // Генерим урл на пост - категорию
    public function getUrl(&$post)
    {
        if (!isset($post['id'])) return '';

        $res = '';

        $url = (isset($post['url']) && $post['url'] != '') ? $post['url'] : $post['id'];

        foreach ($this->getPath($post['category_id']) as $cat) {
            if ($cat['url'] != '') $res .= '/' . $cat['url'];
        }

        return url($res . '/' . $url);
    }

    // Выводим путь до нужной категории
    public function getPath($catID)
    {
        $res = [];
        $this->_getPath($catID, $res);
        return $res;
    }

    private function _getPath($catID, &$res)
    {
        if (isset($this->allCat[$catID])) {
            if ($this->allCat[$catID]['category_id'] != 0) {
                $this->_getPath($this->allCat[$catID]['category_id'], $res);
            }
            $res[] = $this->allCat[$catID];
        }
    }

    // Получаем ID корневой записи для catID
    public function getRootCat($catID)
    {
        if (!isset($this->allCat[$catID])) abort(403, 'Categories::getRootCat() категории не существует');

        if ($this->allCat[$catID]['category_id'] == 0) return $this->allCat[$catID];
        else return $this->getRootCat($this->allCat[$catID]['category_id']);
    }

    // Получаем категорию, если $id = false выводим все
    public function getCat($id = false)
    {
        //Выводим все категории
        if ($id === false) return $this->allCat;
        //Если категории нет
        if (!isset($this->allCat[$id])) return false;
        //Выводим нужную категорию
        return $this->allCat[$id];
    }

    // Гереним дерево
    public function getHtmlTree($pref)
    {
        $res = [];

        $offset = (isset($pref['first-offset']) && $pref['first-offset'] && isset($pref['offset'])) ? $pref['offset'] : '';

        $this->genTree($res, $pref, $pref['root'], $offset);

        $res = Helpers::getHtmlOptions($res);

        if (isset($pref['empty']) && $pref['empty']) array_unshift($res, ['value' => $pref['root'], 'label' => '']);

        return $res;
    }

    // получаем список id шников вложенной в указанную категорию
    // resCur == true вывод c текущей категорией
    // Можно пихать любые данные, есть проверка
    public function getListIds($rootCat = 0, $resCur = false)
    {
        $res = [];

        // Проверка на существование
        if ($rootCat !== 0 && !isset($this->allCat[$rootCat])) return [];

        $pref['root'] = $rootCat;
        $this->genTree($res, $pref, $rootCat);

        $res = Helpers::getListIds($res);
        if ($resCur) array_unshift($res, $rootCat);

        return $res;
    }

    // Рекурсивная функция генерит дерево.
    private function genTree(&$res, &$pref, $parentId = 0, $offset = '')
    {
        //Вычисляем смещение если запись не корневая
        if ($parentId != $pref['root']) {
            if (isset($pref['offset'])) $offset .= $pref['offset'];
            elseif (isset($pref['offset_key'])) $offset++;
        }

        foreach ($this->allCat as $id => $cat) {
            if ($cat['category_id'] != $parentId) continue;
            if (isset($pref['exclude']) && is_array($pref['exclude']) && array_search($id, $pref['exclude']) !== false) continue;

            //Устанавливаем смещение, либо доп поле либо префиксы
            if (isset($pref['offset_key'])) $catRes['c_tree_offset'] = $offset;
            elseif (isset($pref['offset'])) {
                $cat['name'] = $offset . $cat['name'];
            }

            $res[] = $cat;

            $this->genTree($res, $pref, $id, $offset);
        }
    }
}

?>
