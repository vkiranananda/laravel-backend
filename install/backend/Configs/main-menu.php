<?php

return [
	[
		'label' => 'Сайт', 
		'icon' => 'globe', 
		'url' => url('/'),
	],[
		'separator' => true,
		'space-top' => 2,
	],[
		'label' => 'Админка главная', 
		'icon' => 'home', 
		'url' => action('\Backend\Home\Controllers\HomeController@index'),
	],[
	],[
		'separator' => true,
	],[
		'type' => 'method',
		'name' => 'category',
		'method' => '\Backend\Category\Services\Menu::getCats',
	],[
		'separator' => true,
	],[
		'label' => 'Разделы', 
		'icon' => 'list-unordered', 
		'url' => action('\Backend\Category\Controllers\CategoryRootController@index')
	],[
		'label' => 'Опции', 
		'icon' => 'key', 
		'url' => action('\Backend\Option\Controllers\OptionController@index')
	],[
		'label' => 'Конструктор меню', 
		'icon' => 'list-unordered', 
		'url' => action('\Backend\MenuBuilder\Controllers\MenuBuilderController@index')
	],[
		'label' => 'Пользователи', 
		'icon' => 'organization', 
		'url' => action('\Backend\User\Controllers\UserController@index')
	],[
		'label' => 'Роутинг', 
		'icon' => 'terminal', 
		'space-top' => 2,
		'url' => action('\Backend\Category\Controllers\InfoController@routes')
	],
];
