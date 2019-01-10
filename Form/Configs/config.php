<?php
	// Default config file
	return [
		'list' => [
			// Количество записей на странице
			'count-items' => 30,
			// Меню элемента
			'menu-item' =>[
				[ 'label' => 'Править',	'link' => 'edit', 'icon' => 'pencil' ],
				[ 'label' => 'Удалить',	'link' => 'destroy', 'icon' => 'trashcan' ]
			],
		],
		'edit' => [
			'template' => 'Form::edit',
		],
		'upload' => [
			// Контролер для работы с загрузкой файлов, лучше не менять, там много что на это завязано
			'controller' => 'UploadController',
			'enable' => false,
		],
	];