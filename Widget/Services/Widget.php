<?php
namespace Backend\Root\Widget\Services;

use Cache;

/////Доделать как надо будет...

class Widget {
    // Выводим виджет. 
    // $name = category-tree будет искать в App\Widgets\CategoryTree
    // $name = category::category-tree, будет искать в Backend\Category\Widgets\CategoryTree  
    // если не нашел то в Backend\Root\Category\Widgets\CategoryTree
    // Если класс не найден выведет пустое значние
    // $params - Параметры передваемые виджету
    // $cache - Массив опция для кэширования. time = 43200, tags = widgets, name = $name

    static public function print($name, $params = '', $cache = [])
    {
        $timeCache = ( isset($cache['time']) ) ? $cache['time'] : 0; //43200;//30 days
        $tagsCache = ( isset($cache['tags']) ) ? $cache['tags'] : 'widgets';
        $nameCache = ( isset($cache['name']) ) ? $cache['name'] : $name;

        return Cache::tags($tagsCache)->remember($nameCache, $timeCache, function() use ($name, $params)
        {
        	// Преобразуем строку если в ней есть - то убираем тире и делаем следующий символ заглвным
        	
            $name_arr = explode('::', $name);

            if (count($name_arr) > 1) {
            	$name = $name_arr[1];
            	$mod = $name_arr[0];
            }

            // Преобразуем символы
            $name = str_replace("-","", ucwords($name, '-'));
            $name = ucfirst($name);


            // Если в бэкенде  category::category-tree
            if (count($name_arr) > 1) {
            	$name = '\\'.ucfirst($mod).'\Widgets\\'.$name;
            	$class = '\Backend'.$name;

            	// Ищем в Backend\Root
            	if (!class_exists($class)) {
            		$class = '\Backend\Root'.$name;
            		if(! class_exists($class)) return '';
            	}
            } else {
            	$class = '\App\Widgets\\'.$name;
            	if (!class_exists($class)) return '';
            }
            
            $class = new $class();

            $res = $class->show($params);

            if (is_object($res) && class_basename($res) == 'View') return $res->render();

            return $res;
        }); 
    }

}
?>