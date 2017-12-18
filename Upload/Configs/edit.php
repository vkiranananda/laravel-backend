<?php
return [
	'conf' => [
		'save-info-url' => action('\Backend\Root\Upload\Controllers\EditController@saveInfo'),
		'get-info-url' => action('\Backend\Root\Upload\Controllers\EditController@getInfo', ''),
	], 
	'fields' => [
		'desc' => [
		    'type' => 'text', 
		    'name' => 'desc', 
		    'label' => 'Описание',
		    'field-save' => 'array',
		],
		'img_title' => [
		    'type' => 'text', 
		    'name' => 'img_title', 
		    'label' => 'Title',
		    'field-save' => 'array',
		],
		'img_alt' => [
		    'type' => 'text', 
		    'name' => 'img_alt', 
		    'label' => 'Alt',
		    'field-save' => 'array',
		],
	],
];
?>