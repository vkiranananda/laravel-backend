<?php

// Если нужно добавить группу полей то нужно делать через group
	return [
        'list' => [
        	[ 'name' => 'name', 'icon' => 'file', 'link' => 'edit-show', 'sortable' => true ],
        	[ 'name' => 'type', 'sortable' => true, 'attr' => [ 'width' => '100px' ] ],
        	[ 'name' => 'autoload', 'sortable' => true, 'attr' => [ 'width' => '100px' ] ]
        ],
		'search' => [
          	[ 'name' => 'search', 'type' => 'text', 'fields' => [ 'name' ] ]
        ],
		'fields' => [
	        'name' => [
	            'type' => 'text',
	            'name' => 'name',
	            'label' => 'Ключ',
	            'validate' => 'required|unique:options,name,NULL,id,deleted_at,NULL',
	        ],
	        'type' => [
	            'type' => 'select',
	            'name' => 'type',
	            'label' => 'Тип',
	            'options' => [
	            	['label' => 'Текст', 'value' => 'text'],
	            	['label' => 'Редактор', 'value' => 'editor'],
	            	['label' => 'Галерея', 'value' => 'gallery'],
	            	['label' => 'Загрузить файлы', 'value' => 'files'],
	            ],
	            'field-save' => 'array',
	            'value' => 'gallery'
	        ],
			'gallery' => [
	            'type' => 'gallery',
	            'name' => 'gallery',
	            'label' => 'Галерея',
	            'field-save' => 'array',
	     		'show' => [
					['field' => 'type', 'value' => 'gallery', 'type' => '=='],
				],
			],
			'files' => [
	            'type' => 'files',
	            'name' => 'files',
	            'label' => 'Файлы',
	            'field-save' => 'array',
	     		'show' => [
					['field' => 'type', 'value' => 'files', 'type' => '=='],
				],
			],
			'editor' => [
		        'type' => 'editor',
		        'name' => 'editor',
		        'label' => 'Текст',
		        'upload' => true,
		        'field-save' => 'array',
	     		'show' => [
					['field' => 'type', 'value' => 'editor', 'type' => '=='],
				],
	        ],
			'text' => [
		        'type' => 'textarea',
		        'name' => 'text',
		        'label' => 'Значение',
		        'attr' => [ 'rows' => '15' ],
		        'field-save' => 'array',
	     		'show' => [
					['field' => 'type', 'value' => 'text', 'type' => '=='],
				],
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
				'label' => 'Основные',
				'name' => 'main',
				'fields' => [ 'name', 'type', 'gallery','files', 'editor', 'text', 'autoload' ],
			],
		],
	];
