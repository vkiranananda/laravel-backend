<?php
	return [

        'list' => [
        	[ 'name' => 'id', 'label' => 'ID',  'attr' => [ 'width' => '30px' ] ],
        	[ 'name' => 'name', 'icon' => 'file', 'link' => 'edit', 'sortable' => true ],
        	[ 'name' => 'updated_at', 'label' => 'Дата модификации', 'attr' => [ 'width' => '190px' ], 'sortable' => true ]
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

    		'seo' => [
    			'name' => 'seo',
    			'type' => 'group',
    			'load-from' => 'seo-fields',
    			'field-save' => 'array' 
    		],

	        'template' => [ 
	        	'name' => 'template',
        		'type' => 'select', 
        		'field-save' => 'array',
        		'label' => 'Шаблон', 
        		'options' => [
        			[ 'value' => '', 'label' => 'По умолчанию' ],
	         		[ 'value' => 'contacts', 'label' => 'Контакты' ],
	         		[ 'value' => 'info', 'label' => 'Инфо' ],
        		],
    		],
    		'category_id' => [ 
	        	'name' => 'category_id',
        		'type' => 'select', 
        		'name' => 'category_id', 
        		'label' => 'Категория', 
    		],
			'url' => [
	            'name' => 'url',
	            'type' => 'text', 
	            'label' => 'URL',
	            'validate' => 'nullable|alpha_dash|unique:pages,url',
	        ],
	    ],

		'edit' => [
			'default' => [
				'label' => 'Основные',
				'name' => 'default',
				'fields' => [ 'name','text' ],
			],
			'attr' => [
				'label' => 'Атрибуты',
				'name' => 'attr',
				'fields' => [ 'category_id' ],
			],
			'seo' => [
				'label' => 'SEO',
				'name' => 'seo',
				'fields' => [ 'url', 'seo' ],
			]
		],
		'search' => [
          	[
          		'name' => 'search',
          		'type' => 'text',
        		'attr' => [ 'placeholder' => 'Введите текст для поиска' ],
          		'fields' => [ 'name', 'text' ],
          	],[
          		'options-empty' => 'Все категории',
          		'name' => 'parent-cat',
          		'field-from' => 'category_id',
          		'label' => '',
          		'fields' => [ 'category_id'],
          	],
        ]
    ];