<?php
	return [
		'lang' => [
			'list-title' => 'Редиректы',
			'create-title' => 'Создать редирект',
			'edit-title' => 'Редактировать редирект',
		],


        'list' => [
        	[ 'name' => 'from_url', 'icon' => 'file', 'link' => 'edit'],
        	[ 'name' => 'to_url' ],
        	[ 'name' => 'enable' ],
        ],

		'fields' => [
			'from_url' => [
	            'type' => 'text', 
	            'name' => 'from_url', 
	            'label' => 'Откуда',
	            'validate' => 'required|unique:redirects,from_url',
	        ],
	        'to_url' => [
	            'type' => 'text', 
	            'name' => 'to_url', 
	            'label' => 'Куда',
	            'validate' => 'required',
	        ],
	        'enable' => [
	            'type' => 'radio', 
	            'name' => 'enable', 
	            'label' => 'Включен',
	            'value' => '1',
	            'options' => [
	            	['value' => '1', 'label' => 'Да'],
	            	['value' => '0', 'label' => 'Нет'],
	            ],
	        ],
	    ],

		'edit' => [
			'default' => [
				'tab_name' => 'Основные',
				'id' => 'main',
				'fields' => [
					[ 'name' => 'from_url' ],
			        [ 'name' => 'to_url' ],
			        [ 'name' => 'enable' ],
			    ],
			],
		],
	];