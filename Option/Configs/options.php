<?php
	return [
		'lang' => [
			'list-title' => 'Список "ключ-значение"',
			'create-title' => 'Создать "ключ-значение"',
			'edit-title' => 'Редактирование "ключ-значение"',
		],
	    'conf' => [
	        'media-files' => 'hidden',
	        'store-redirect' => true,
        ],

        'list' => [
        	[ 'name' => 'name', 'icon' => 'file', 'link' => 'edit'],
          ],

		'fields' => [
	        'name' => [
	            'type' => 'text', 
	            'name' => 'name', 
	            'label' => 'Ключ',
	            'validate' => 'required|not_in:general|unique:options,name',
	        ],
	        'type' => [	
	            'type' => 'select', 
	            'name' => 'type', 
	            'label' => 'Тип',
	            // 'value' => ''
	            'options' => [
	            	['label' => 'Текст', 'value' => 'text'],
	            	['label' => 'Редактор', 'value' => 'mce'],
	            	['label' => 'Меню', 'value' => 'menu'],
	            	['label' => 'Галерея', 'value' => 'gallery'],
	            ],
	            'field-save' => 'array',
	        ],
	        'autoload' => [
	            'type' => 'radio', 
	            'name' => 'autoload', 
	            'label' => 'Автозагрузка',
	            'desc' => 'Если эта опция используется постоянно, то укажите Да, если только на некоторых страницах тогда Нет',
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
					[ 'name' => 'name' ],
					[ 'name' => 'value' ],
			        [ 'name' => 'type' ],
			   		[ 'name' => 'autoload' ],
			    ],
			],
		],
	];