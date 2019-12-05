<?php

namespace Backend\News\Controllers;
use Backend\News\Models\News;
use Categories;

class SitemapController extends \Backend\Sitemap\Controllers\BaseSitemapController
{
	
	public function make()
	{
		News::whereIn('category_id', $this->getCatsId())
			->select('id', 'category_id', 'url', 'updated_at')
			->where('status', 1)
    		->where('publication_date', '<=', date("Y-m-d"))
    		->chunk(10000, function ($news) {
    			foreach ($news as $oneNews) {
       				$this->addUrlbyCat(
       					Categories::getUrl($oneNews), 
       					$oneNews['category_id'], 
       					$oneNews['updated_at']
       				);
    			}
    		});
	}
}