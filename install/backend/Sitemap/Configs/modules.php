<?php
	return [
		'page' => [
			'controller' => '\Backend\Page\Controllers\SitemapController',
			'category-mod' => 'Page',
			// 'default' => [
			// 	'freq' => 'monthly',
			// 	'priority' => '1.0',
			// ]
		],
		'news' => [
			'controller' => '\Backend\News\Controllers\SitemapController',
			'category-mod' => 'News',
		],
		'blog' => [
			'controller' => '\Backend\Blog\Controllers\SitemapController',
			'category-mod' => 'Blog',
		],
	];