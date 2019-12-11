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
		// Если разделы с одинаковыми урлами, то по умолчанию все пойдет по этому
		'main-route' => true,
	]
];
