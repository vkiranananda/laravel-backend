<?php

	return [
        'list' => [
        	[ 'name' => 'name', 'icon' => 'file', 'link' => 'edit-show', 'sortable' => true],
        	[ 'name' => 'category', 'label' => 'Категория', 'attr' => [ 'width' => '20%' ], 'func' => 'indexCategoryField', 'link' => 'category' ],
        	[ 'name' => 'publication_date', 'label' => 'Дата публикации', 'attr' => [ 'width' => '190px;' ], 'sortable' => true ]
        ],
		'fields' => [
	        'name' => [
	            'type' => 'text',
	            'name' => 'name',
	            'label' => 'Заголовок',
	            'validate' => 'required',
	        ],
	        'announcement' => [
	        	'name' => 'announcement',
	            'type' => 'textarea',
	            'label' => 'Анонс',
	            'field-save' => 'array',
	            'attr' => [ 'rows' => '5']
	        ],
	        'text' => [
	        	'name' => 'text',
	            'type' => 'editor',
	            'label' => 'Текст',
	            'format' => 'fool',
	            'size' => 'normal',
	            'upload' => true,
	        ],
			'url' => [
	            'name' => 'url',
	            'type' => 'text',
	            'label' => 'URL',
	            'validate' => 'nullable|alpha_dash|unique:news,url',
	        ],
	        'category_id' => [
	        	'name' => 'category_id',
        		'type' => 'select',
        		'label' => 'Категория',
    		],
    		'publication_date' => [
    			'name' => 'publication_date',
	            'type' => 'date',
	            'label' => 'Дата публикации',
	            'value' => 'now',
	            'validate' => 'required|date',
			],
			'status' => [
				'name' => 'status',
	        	'type' => 'select',
	            'label' => 'Статус',
	            'options' => [
	         		[ 'value' => '1', 'label' => 'Опубликован' ],
	         		[ 'value' => '0', 'label' => 'Черновик' ],
	         	],
			],
			'icon' => [
				'name' => 'icon',
	            'type' => 'gallery',
	            'label' => 'Иконка',
	            'field-save' => 'array',
	            'max-files' => 1
	        ],
    		'seo' => [
    			'name' => 'seo',
    			'type' => 'group',
    			'load-from' => 'seo-fields',
    			'field-save' => 'array'
    		],
	    ],

		'edit' => [
			'main' => [
				'label' => 'Основные',
				'name' => 'main',
				'fields' => ['name', 'announcement', 'text' ]
			],'attr' => [
				'label' => 'Атрибуты',
				'name' => 'attr',
				'fields' => [ 'category_id', 'publication_date', 'status', 'icon' ],
			],'seo' => [
				'label' => 'SEO',
				'name' => 'seo',
				'fields' => [ 'url', 'seo' ]
			]
		],
		'search' => [
          	[
          		'name' => 'search',
          		'type' => 'text',
        		'attr' => [ 'placeholder' => 'Введите текст для поиска'	],
          		'fields' => [ 'name', 'text' ],
          	],[
          		'options-empty' => 'Все категории',
          		'name' => 'parent_cat',
          		'field-from' => 'category_id',
          		'label' => '',
          		'fields' => [ 'category_id'],
          		'search-type' => 'filter',
          	],
        ],
	];
