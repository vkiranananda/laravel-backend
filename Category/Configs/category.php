<?php
return array_merge_recursive( [
		'lang' => [
			'create-title' => 'Новая категория',
			'edit-title' => 'Редактирование категории',
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
	        'parent_id' => [ 
	        	'name' => 'parent_id',
        		'type' => 'select', 
        		'label' => 'Родительская категория', 
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
	        'gallery' => [	
	        	'name' => 'gallery',	
	            'type' => 'gallery', 
	            'label' => 'Галерея',
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
	            'upload' => true
	        ],      	
        ],
		'edit' => [
			'default' => [
				'tab_name' => 'Основные',
				'id' => 'main',
				'fields' => [
			        [ 'name' => 'name' ],
			        [ 'name' => 'parent_id' ],
            		[ 'name' => 'url' ],
            		[ 'name' => 'sort_num' ],
            		[ 'name' => 'desc', 'attr' => [	'rows' => '8' ] ],
            		[ 'name' => 'gallery' ],
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
	], GetConfig::backend('seo-fields'),  GetConfig::backend('Sitemap::fields')
);