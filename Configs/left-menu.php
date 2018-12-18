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
		'url' => url('/'),
	],[
		'separator' => true,
	],[
		'category' => true,
	],[
		'separator' => true,
	],[
		'label' => 'Разделы', 
		'icon' => 'list-unordered', 
		'url' => action('\Backend\Root\Category\Controllers\CategoryRootController@index')
	],[
		'label' => 'Ключ-значение', 
		'icon' => 'key', 
		'url' => action('\Backend\Root\Option\Controllers\OptionController@index')
	],[
		'label' => 'Настройки', 
		'icon' => 'gear', 
		'url' => action('\Backend\Root\Option\Controllers\OptionGeneralController@edit')
	],[
		'label' => 'Пользователи', 
		'icon' => 'organization', 
		'url' => action('\Backend\Root\User\Controllers\UserController@index')
	],[
		'label' => 'Роутинг', 
		'icon' => 'terminal', 
		'space-top' => 2,
		'url' => action('\Backend\Root\Info\Controllers\InfoController@routes')
	],
];
