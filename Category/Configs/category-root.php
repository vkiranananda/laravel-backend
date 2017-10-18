<?php
	
	return  array_merge_recursive( [
		'lang' => [
			'create-title' => 'Новый раздел',
			'edit-title' => 'Редактирование раздела',
		],
		'conf' => [
		    'media-files' => 'hidden',
		    'update-redirect' => true,
		    'store-redirect' => true,
	        'load-scripts' => [
        		"/js/tinymce/tinymce.min.js",
        	],
		],
		'fields' => [
	        'name' => [
	        	'name' => 'name',
	            'type' => 'text', 
	            'label' => 'Название',
	            'validate' => 'required',
	        ],
	        'mod' =>[
	        	'name' => 'mod',
	            'type' => 'select',  
	            'label' => 'Модуль',
	        ],
	        'conf-type' => [ 
	        	'name' => 'conf-type',      			   
	            'type' => 'select', 
	            'field-save' => 'array',
	            'label' => 'Структура',
	            'options' => [
	         		[ 'value' => '', 'label' => 'Обычная' ],
	         		[ 'value' => 'hierarchical', 'label' => 'Древовидная' ],
	         	],
			],
			'sort_num' => [
				'name' => 'sort_num',
	            'type' => 'text', 
	            'label' => 'Номер сортировки',
	            'validate' => 'integer',
	            'value' => '0',
	        ],
	        'desc' => [
	        	'name' => 'desc',
	            'type' => 'textarea',  
	            'label' => 'Описание',
	        ],
	        'conf-count-posts' => [
	        	'name' => 'conf-count-posts',
	            'type' => 'text',  
	            'label' => 'Количество записей на странице',
	            'value' => '12',
	            'desc' > '0 вывести все записи',
	            'field-save' => 'array',
	            'validate' => 'alpha_num',
	        ],
	        'lang-list-title' => [
	        	'name' => 'lang-list-title',
	            'type' => 'text', 
	            'label' => 'Заголовок в списке',
	            'value' => 'Список записей',
	            'field-save' => 'array',
	        ],
	        'lang-create-title' => [
	        	'name' => 'lang-create-title',
	            'type' => 'text', 
	            'label' => 'Новая запись',
	            'value' => 'Новая запись',
	            'field-save' => 'array',
	        ],
	        'lang-edit-title' => [
	        	'name' => 'lang-edit-title',
	            'type' => 'text',  
	            'label' => 'Редактировать запись',
	            'value' => 'Редактируем запись',
	            'field-save' => 'array',
	        ],
	        'post_name' => [
	        	'name' => 'post_name',
	            'type' => 'text', 
	            'label' => 'Заголовок',
	        ],
	        'post_text' => [
	        	'name' => 'post_text',
	            'type' => 'mce', 
	            'label' => 'Текст',
	            'upload' => true,
	        ],
		],
		'edit' => [
			'default' => [
				'tab_name' => 'Основные',
				'id' => 'main',
				'fields' => [
			        [ 'name' => 'name' ],
			        [ 'name' => 'mod' ],
			        [ 'name' => 'conf-type' ],
			        [ 'name' => 'sort_num' ],
			        [ 'name' => 'desc', 
			            'attr' => ['rows' => '8'] 
			        ],
			    ],
			],'conf' => [
				'tab_name' => 'Настройки',
				'id' => 'conf',		
				'fields' => [
			        [ 'name' => 'conf-count-posts' ],
			        [ 'name' => 'lang-list-title' ],
			        [ 'name' => 'lang-create-title' ],
			        [ 'name' => 'lang-edit-title' ],
			    ],
			],'post' => [
				'tab_name' => 'Страница',
				'id' => 'post',
				'fields' => [
			        [ 'name' => 'post_name' ],
			        [ 'name' => 'post_text', 'attr' => [ 'height' => '500' ] ],
			    ],
			],
		],
	], GetConfig::backend('seo-fields'), GetConfig::backend('Sitemap::fields') 
);