<?php

	return [
        'list' => [
        	[ 'name' => 'name', 'icon' => 'file', 'link' => 'edit-show', 'sortable' => true],
        ],
		'fields' => [
	        'name' => [
	            'type' => 'text',
	            'name' => 'name',
	            'label' => 'Название',
	            'validate' => 'required|unique:menu_builder,name,NULL,id,deleted_at,NULL',
	        ],
	        'menu' => [
	            'type' => 'menu-builder',
	            'name' => 'menu',
	            'label' => 'Дерево меню',
	        ],
	    ],

		'edit' => [
			'main' => [
				'label' => 'Основные',
				'name' => 'main',
				'fields' => ['name', 'menu' ]
			],
		],
	];
