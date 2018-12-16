<?php

return [
	[
		'label' => 'Сайт', 
		'icon' => 'home', 
		'url' => url('/'),
		'space-bottom' => 4,	//0-4
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
