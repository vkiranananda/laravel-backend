<?php

namespace Backend\Page\Controllers;
use Backend\Page\Models\Page;
use Categories;
use Helpers;

class SitemapController extends \Backend\Sitemap\Controllers\BaseSitemapController
{
	
	public function make()
	{
		Page::whereIn('category_id', $this->getCatsId())
			->select('id', 'category_id', 'url', 'updated_at', 'array_data')
    		->chunk(10000, function ($page) {
    			foreach ($page as $onePage) {
    				$sitemap = Helpers::getDataField($onePage, 'sitemap', []);

    				if (isset($sitemap['enable']) && $sitemap['enable'] == 0) continue;

    				$url = Categories::getUrl($onePage);

    				// Подменяем урл главной страницу
    				if ($onePage['url'] == 'index') $url = preg_replace('/(.*)\/index$/', '${1}', $url);

       				$this->addUrlbyCat(
       					$url, 
       					$onePage['category_id'], 
       					$onePage['updated_at'],
       					(isset($sitemap['freq'])) ? $sitemap['freq'] : '',
       					(isset($sitemap['priority'])) ? $sitemap['priority'] : ''
       				);
    			}
    		});
	}
}