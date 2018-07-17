<?php

	$sitemap = GetConfig::backend('Sitemap::fields');
	unset($sitemap['edit']['sitemap']['fields']['sitemap-link-enable']);
	$sitemap['fields']['sitemap-enable']['desc'] = "Включить отображение данной страницы на карте сайта";
	$sitemap['edit']['sitemap']['fields'][] = [ 'name' => 'sitemap-url' ];

	return array_merge_recursive( [
        'conf' => [
			'breadcrumb' => true,
	        'media-files' => 'hidden',
	        'load-scripts' => [
        		"/js/tinymce/tinymce.min.js",
        	],
        ],

        'list' => [
        	[ 'name' => 'id', 'label' => 'ID',  'attr' => [ 'width' => '30px' ] ],
        	[ 'name' => 'name', 'icon' => 'file', 'link' => 'edit'],
        	[ 'name' => 'updated_at', 'label' => 'Дата модификации', 'attr' => [ 'width' => '190px' ] ]
        ],

		'fields' => [
	        'name' => [
	        	'name' => 'name',
	            'type' => 'text', 
	            'label' => 'Заголовок',
	            'validate' => 'required',
	        ],
	        'text' => [	
	        	'name' => 'text',
	            'type' => 'mce', 
	            'label' => 'Текст',
	            'upload' => true,
	        ],
	        'category_id' => [ 
	        	'name' => 'category_id',
        		'type' => 'select', 
        		'label' => 'Категория', 
    		],
    		'gallery' => [	
    			'name' => 'gallery',	
	            'type' => 'gallery', 
	            'label' => 'Галерея',
        		'field-save' => 'array',
	        ],
	        'template' => [ 
	        	'name' => 'template',
        		'type' => 'select', 
        		'field-save' => 'array',
        		'label' => 'Шаблон', 
        		'options' => [
        			[ 'value' => '', 'label' => '' ],
	         		[ 'value' => 'contacts', 'label' => 'Контакты' ],
        		],
    		],
			'sitemap-url' => [
				'name' => 'sitemap-url',
        		'type' => 'text', 
        		'label' => 'Альтернативный URL',
        		'desc' => 'Например для главной страницы можно указать "/". URL указывается относительный от корня сайта.',
        		'field-save' => 'array',
    		],
	    ],

		'edit' => [
			'default' => [
				'tab_name' => 'Основные',
				'id' => 'main',
				'fields' => [
			        [ 'name' => 'name' ],
			        [ 'name' => 'text', 'attr' => [ 'rows' => '25' ] ],
			    ],
			],'attr' => [
				'tab_name' => 'Атрибуты',
				'id' => 'attr',
				'fields' => [
			        [ 'name' => 'category_id' ],
			        [ 'name' => 'gallery' ],
			        [ 'name' => 'template' ]
			    ],
			],
		],
		'search' => [
          	[
          		'name' => 'search',
          		'type' => 'text',
        		'attr' => [
        			'placeholder' => 'Введите текст для поиска',
        		],
        		'conteiner-class' => 'col-4',
          		'fields' => [ 'name' ],
          	],
        ],
	], GetConfig::backend('seo-fields'), $sitemap
);