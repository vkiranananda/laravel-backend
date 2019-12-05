<?php
	// Default config file
	return [
		'list' => [
			// Количество записей на странице
			'count-items' => 30,
			// Создание записей из списка
			'create' => true,
			//Кнопки редактирования, удаления и клонирования у каждой записи
			'item-edit' => true,
			'item-destroy' => true,
			'item-clone' => true,
			// Меню элемента
			'item-menu-default' => [
				'edit' => [ 'label' => 'Править', 'link' => 'edit', 'icon' => 'pencil' ],
				'clone' => [ 'label' => 'Клонировать', 'link' => 'clone', 'icon' => 'file-symlink-file' ],
				'destroy' => [ 'label' => 'Удалить', 'link' => 'destroy', 'icon' => 'trashcan', 'confirm' => 'Вы действительно хотите удалить эту запись?' ]
			],
			'default-order' => ['col' => 'id', 'type' => 'desc'],
			// Сортировка списка перетаскиванием объектов
			'sortable' => false,
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