<?php

namespace Backend\BaseMod\Controllers;
use Backend\BaseMod\Models\BaseMod;
use Categories;
use Helpers;

class SitemapController extends \Backend\Sitemap\Controllers\BaseSitemapController
{
	
	public function make()
	{
		BaseMod::whereIn('category_id', $this->getCatsId())
			->select('id', 'category_id', 'url', 'updated_at')
    		->chunk(10000, function ($page) {
    			foreach ($page as $onePage) {
    				$sitemap = Helpers::getDataField($onePage, 'sitemap', []);

       				$this->addUrlbyCat(
       					Categories::getUrl($onePage), 
       					$onePage['category_id'], 
       					$onePage['updated_at']
       				);
    			}
    		});
	}
}