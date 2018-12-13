<?php

return [
	[
		'label' => 'Сайт', 
		'icon' => 'home', 
		'url' => url('/'),
		'space-bottom' => 4,	//0-4
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
		'icon' => 'users', 
		'url' => action('\Backend\Root\User\Controllers\UserController@index')
	],[
		'label' => 'Роутинг', 
		'icon' => 'terminal', 
		'space-top' => 2,
		'url' => action('\Backend\Root\Info\Controllers\InfoController@routes')
	],
];
