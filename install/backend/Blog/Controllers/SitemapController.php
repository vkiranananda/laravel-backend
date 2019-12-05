<?php

namespace Backend\Blog\Controllers;
use Backend\Blog\Models\Blog;
use Categories;

class SitemapController extends \Backend\Sitemap\Controllers\BaseSitemapController
{
	
	public function make()
	{
		Blog::whereIn('category_id', $this->getCatsId())
			->select('id', 'category_id', 'url', 'updated_at')
			->where('status', 1)
    		->where('publication_date', '<=', date("Y-m-d"))
    		->chunk(10000, function ($blog) {
    			foreach ($blog as $oneBlog) {
       				$this->addUrlbyCat(
       					Categories::getUrl($oneBlog), 
       					$oneBlog['category_id'], 
       					$oneBlog['updated_at']
       				);
    			}
    		});
	}
}