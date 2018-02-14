<?php

	return array_merge_recursive( [
        'conf' => [
        	'use-category' => true,
			'breadcrumb' => true,
			'order-by' => 'publication_date',
			'order-by-type' => 'desc',
        	'media-files' => 'hidden',
	        'load-scripts' => [
        		"/js/tinymce/tinymce.min.js",
        	],
        ],

        'list' => [
        	[ 'name' => 'name', 'icon' => 'file', 'link' => 'edit'],
        	[ 'name' => 'updated_at', 'label' => 'Дата модификации', 'attr' => [ 'width' => '190px;' ] ]
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
	        ],
	        'text' => [
	        	'name' => 'text',
	            'type' => 'mce', 
	            'label' => 'Текст новости',
	            'upload' => true,
			],

	        'category_id' => [ 
	        	'name' => 'category_id',
        		'type' => 'select', 
        		'name' => 'category_id', 
        		'label' => 'Категория', 
    		],
    		'publication_date' => [
    			'name' => 'publication_date',
	            'type' => 'date', 
	            'label' => 'Дата публикации',
	            'value' => 'now',
	            'validate' => 'date|date_format:Y-m-d',
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
			'gallery' => [	
				'name' => 'gallery',	
	            'type' => 'gallery', 
	            'label' => 'Галерея',
	            'field-save' => 'array',
	        ],
	    ],

		'edit' => [
			'default' => [
				'tab_name' => 'Основные',
				'id' => 'main',
				'fields' => [
			        [ 'name' => 'name', ],
			        [ 'name' => 'announcement', 'attr' => [ 'rows' => '5'] ],
			        [ 'name' => 'text', 'attr' => [ 'rows' => '15' ] ],
			    ],
			],'attr' => [
				'tab_name' => 'Атрибуты',
				'id' => 'attr',
				'fields' => [
			        [ 'name' => 'category_id' ],
            		[ 'name' => 'publication_date' ],
					[ 'name' => 'status' ],
					[ 'name' => 'gallery' ],
			    ],
			],
		],
	], GetConfig::backend('seo-fields')
);