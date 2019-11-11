<?php
return [
	'News' => [
		'resourceController' => '\Backend\News\Controllers\NewsController',
		'viewControllerAction' => '\App\Http\Controllers\NewsController',
		'categoryController' => '\App\Http\Controllers\NewsController',
		'label' => 'Новости',
	],
	'Page' => [
		'resourceController' => '\Backend\Page\Controllers\PageController',
		'viewControllerAction' => '\App\Http\Controllers\PageController',
		'categoryController' => '\App\Http\Controllers\PageController',
		'label' => 'Страницы',
	],
	// 'Blog' => [
	// 	'resourceController' => '\Backend\Blog\Controllers\BlogController',
	// 	'viewControllerAction' => '\App\Http\Controllers\BlogController',
	// 	'categoryController' => '\App\Http\Controllers\BlogController',
	// 	'label' => 'Блог',
	// ],
];
