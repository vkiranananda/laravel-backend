<?php

namespace Backend\Sitemap\Controllers;

use App\Http\Controllers\Controller;
use Helpers;
use Response;
use Categories;
use GetConfig;

class SitemapController extends Controller
{
	// Рекурсивно применяем настройки категориям
    private function categorySetData(&$res, &$cats, &$parentCat )
    {
        foreach ($cats as &$cat) {
        	// Если не соответсвует родительской категории
            if ($cat['category_id'] != $parentCat['id']) continue;

            // Получаем данные
            $sitemap = Helpers::getDataField($cat, 'sitemap', []);

            // Если категория вырублена пропускаем ее
            if (isset($sitemap['enable']) && $sitemap['enable'] == '0') continue;

			$curCat['id'] = $cat['id'];
		
			// Получаем значние по умолчанию
			$curCat['freq'] = (!isset($sitemap['freq']) || $sitemap['freq'] == '') 
				?  $parentCat['freq'] : $sitemap['freq'];
			$curCat['priority'] = (!isset($sitemap['priority']) || $sitemap['priority'] == '') 
				? $parentCat['priority'] : $sitemap['priority'];
		
			// По умолчанию линк включен
			$curCat['url'] = (isset($sitemap['link-enable']) && $sitemap['link-enable'] == 0) 
				? false : Categories::getUrl($cat);

			// Получаем дату
            $curCat['date'] = date("c", strtotime($cat['updated_at']));

            $res[$cat['id']] = $curCat;

            // Структура линейная
            if ($parentCat['id'] == 0 && Helpers::getDataField($cat, 'conf-type', '') == '') continue;

            // Пробираемся в глубину дальше...
            $this->categorySetData($res, $cats, $curCat);
        }
    }

    public function index($url = false)
    {
    	$modules = GetConfig::backend('Sitemap::modules');
 
        if ($url) {
        	$url = explode('-', $url, 2);

        	$mod = $url[0];
        	$page = (isset($url[1])) ? (int)$url[1] : 1;

        	// Проверяем на валидность страницы
        	if (!isset($modules[$mod]) || !is_int($page) || $page < 1 ) abort(404);

        	$modArr = $modules[$mod];

        	// Устанавливаем значение по умолчанию
        	$freq = (isset($modArr['default']['freq'])) ? $modArr['default']['freq'] : '';
        	$priority = (isset($modArr['default']['priority'])) ? $modArr['default']['priority'] : '';

        	// Если категории
        	if (isset($modules[$mod]['category-mod'])) {
        		$allCats = [];
	            // Базовые значения параметров для категории
                $parentEl = [ 
                	'id' => 0, 
                	'freq' => $freq, 
                	'priority' => $priority, 
               	];

               	// Получаем все категории в данном модуле
               	$req = \Backend\Category\Models\Category::where('mod', $modules[$mod]['category-mod'])
	            		->get(['id', 'category_id', 'array_data', 'updated_at', 'url']);
               	// Рекурсивно добиваем значения.
	            $this->categorySetData($allCats, $req, $parentEl);

	        } else {
	        	$allCats = [];
	        }

	        $sitemapController = new $modArr['controller']();

	        $sitemapController->setCats($allCats)->make();

            return response(view('Sitemap::urlset', [ 
            	'data' => array_merge($allCats, $sitemapController->getUrls())
            ] ))->withHeaders(['Content-Type' => 'text/xml']);

        } 
        // Index /sitemap.xml
        elseif($url == '') {
            return response( view('Sitemap::sitemapindex', [ 'data' => $modules ] ))
                   ->withHeaders(['Content-Type' => 'text/xml']);
        }
        
        abort(404);
    }

}
