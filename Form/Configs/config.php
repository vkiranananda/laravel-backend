<?php
	// Default config file
	return [
		'list' => [
			// Количество записей на странице
			'count-items' => 30,
			// Создание записей из списка
			'create' => true,
			// Меню элемента
			'item-menu-default' => [
				'edit' => [ 'label' => 'Править', 'link' => 'edit', 'icon' => 'pencil' ],
				'destroy' => [ 'label' => 'Удалить', 'link' => 'destroy', 'icon' => 'trashcan', 'confirm' => 'Вы действительно хотите удалить эту запись?' ]
			],
			'default-order' => ['col' => 'id', 'type' => 'desc'],
		],
		'edit' => [
			'template' => 'Form::edit',
		],
		'show' => [
			'template' => 'Form::show',
		],
		'upload' => [
			// Контролер для работы с загрузкой файлов, лучше не менять, там много что на это завязано
			'controller' => 'UploadController',
			'enable' => false,
		],
		'url-params' => [],
	];