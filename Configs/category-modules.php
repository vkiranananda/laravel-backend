<?php
return [
	'Post' => [
		'resourceController' => '\Backend\Post\Controllers\PostController',
		'viewControllerAction' => '\App\Http\Controllers\PostController',
		'categoryController' => '\App\Http\Controllers\PostController',
		'label' => 'Статьи',
	],'News' => [
		'resourceController' => '\Backend\News\Controllers\NewsController',
		'viewControllerAction' => '\App\Http\Controllers\NewsController',
		'categoryController' => '\App\Http\Controllers\NewsController',
		'label' => 'Новости',
	],'Page' => [
		'resourceController' => '\Backend\Page\Controllers\PageController',
		'viewControllerAction' => '\App\Http\Controllers\PageController',
		'categoryController' => '\App\Http\Controllers\PageController',
		'label' => 'Страницы',
	],'Property' => [
		'resourceController' => '\Backend\Property\Controllers\PropertyController',
		'viewControllerAction' => '\App\Http\Controllers\PropertyController',
		'categoryController' => '\App\Http\Controllers\PropertyController',
		'label' => 'Объекты размещения',
	],'Excursion' => [
		'resourceController' => '\Backend\Excursion\Controllers\ExcursionController',
		'viewControllerAction' => '\App\Http\Controllers\ExcursionController',
		'categoryController' => '\App\Http\Controllers\ExcursionController',
		'label' => 'Экскурсии',
	],'Realty' => [
		'resourceController' => '\Backend\Realty\Controllers\RealtyController',
		'viewControllerAction' => '\App\Http\Controllers\RealtyController',
		'categoryController' => '\App\Http\Controllers\RealtyController',
		'label' => 'Недвижимость',
	],
];
