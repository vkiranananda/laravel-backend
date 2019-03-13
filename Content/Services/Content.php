<?php
namespace Backend\Root\Site\Services;
use Categories;
use App;
use \Backend\Root\Upload\Models\MediaFile;
use Cache;
use Helpers;

class Content {
    
    static public function getWidget($name, $params = '', $cache = [])
    {
        $timeCache = ( isset($cache['time']) ) ? $cache['time'] : 43200;//30 days
        $tagsCache = ( isset($cache['tags']) ) ? $cache['tags'] : 'widgets';
        $nameCache = ( isset($cache['name']) ) ? $cache['name'] : $name;

        return Cache::tags($tagsCache)->remember($nameCache, $timeCache, function() use ($name, $params)
        {
            $name = str_replace("-","", ucwords($name, '-'));

            $class = '\App\Widgets\\'.$name;

            if(! class_exists($class)){
                //For backend widgets. Example category::category-tree
                $name = str_replace("::","\\Widgets\\", ucwords($name, '::'));
                $class = '\Backend\Root\\'.$name;
                if(! class_exists($class)) return '';
            }

            $res = $class::show($params);
            if(is_object($res) && class_basename($res) == 'View'){
                return $res->render();
            }

            return $res;
        }); 
    }

    // Генерим урл на пост - категорию
    static public function getUrl(&$post)
    { 
        if (! isset($post['id']) ) return '';

        $url = ( isset($post['url']) ) ? $post['url'] : '' ;

        if ( isset($post['category_id']) ) {
            $category_id = $post['category_id'];
        } else {
            abort(403, 'URLs::get неверный тип данных при создании ссылки');
        }

    	return  Content::getUrlBase($url, $post['id'], $category_id);
	}

    static public function getUrlBase($url, $id, $category_id)
    {
        if($url == '')$url = $id;

        $res = '';

        foreach (Categories::getPath($category_id) as $cat) {
            if($cat['url'] != '')$res .= '/'.$cat['url'];
        }

        return  url($res.'/'.$url);
    }
}
?>