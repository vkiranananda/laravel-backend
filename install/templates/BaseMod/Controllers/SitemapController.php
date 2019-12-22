<?php

namespace Backend\BaseMod\Controllers;
use Backend\BaseMod\Models\BaseMod;
use Helpers;

class SitemapController extends \Backend\Sitemap\Controllers\BaseSitemapController
{
	
	public function make()
	{
		BaseMod::select('id', 'url', 'updated_at')
    		->chunk(10000, function ($page) {
    			foreach ($page as $onePage) {
    				$sitemap = Helpers::getDataField($onePage, 'sitemap', []);
    				// Измени метод получения url, первый параметр
       				$this->addUrlbyCat(
       					$onePage['id'], 
       					$onePage['category_id'], 
       					$onePage['updated_at']
       				);
    			}
    		});
	}
}