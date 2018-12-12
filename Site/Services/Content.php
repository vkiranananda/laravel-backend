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

    //Генерим урл на пост - категорию
    static public function getUrl(&$post)
    { 
        if(!isset($post['id']))return '';

        $url = (isset($post['url'])) ? $post['url'] : '' ;

        if(isset($post['category_id'])){
            $category_id = $post['category_id'];
        }elseif(isset($post['parent_id']) ) {
            $category_id = $post['parent_id'];
        }else {
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

    static public function setHeadersFromPage($page_id, $page_local_id)
    {
        if (App::environment('local')) $page_id = $page_local_id;
        $post = \Backend\Root\Page\Models\Page::findOrFail($page_id);

        return Content::setHeaders($post);
    }

    static public function setHeaders(&$post)
    {
            // dd($post);
        $res = ['seo-description' => ''];
        if(isset($post['array_data']['fields']['seo_title']) && $post['array_data']['fields']['seo_title'] != ''){
            $res['seo-title'] = $post['array_data']['fields']['seo_title'];
        }else {
            $res['seo-title'] = $post['name'];
        }

        if(isset($post['array_data']['fields']['seo_title_h1']) ){
        	$res['seo_title_h1'] = $post['array_data']['fields']['seo_title_h1'];
        }

        if(isset($post['array_data']['fields']['seo_description']) && $post['array_data']['fields']['seo_description'] != ''){
            $res['seo-description'] = $post['array_data']['fields']['seo_description'];
        }
        $res['title'] = $post['name'];

        if(class_basename($post) == 'Category'){
            if( isset($post['post_text']) && $post['post_text'] != '')$res['text'] = $post['post_text'];
        } else {
            if( isset($post['text']) && $post['text'] != '')$res['text'] = $post['text'];
        }
      //  elseif(isset($post['post_text']) && $post['text'] != '')$res['text'] = $post['text'];

        return ['page' => $res];
    }

    //Выводит значение поля.
    static public function getFieldValue(&$data, &$field)
    {
        $val = Helpers::dataIsSet($data, $field['name']);
        
        if ( isset($field['options']) && is_array($field['options']) ) {
            $val = ( isset($field['options'][$val]) ) ? $field['options'][$val] : 'Не установлено' ;
        }
        
        return $val;
    }





    //Получаем все картинки оптом что бы не плодить запросы
    static public function getImgUrlList(&$list, $field)
    {
        return app('UploadedFiles')->getImgUrlList($list, $field);
    }

    static public function uFile()
    {
    	return app('UploadedFiles');
    }

}
?>