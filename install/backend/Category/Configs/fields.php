<?php
	return [
		'list' => [
        	[ 'name' => 'name', 'icon' => 'file', 'link' => 'edit', 'sortable' => true ],
         	[ 'name' => 'category', 'label' => 'Категория', 'attr' => [ 'width' => '20%' ], 'func' => 'indexCategoryField', 'link' => 'category' ],
        	[ 'name' => 'url', 'attr' => [ 'width' => '10%' ] ],
        	[ 'name' => 'updated_at', 'label' => 'Дата модификации', 'attr' => [ 'width' => '190px' ], 'sortable' => true ]
        ],
		'fields' => [
	        'name' => [
	        	'name' => 'name',
	            'type' => 'text', 
	            'label' => 'Название',
	            'validate' => 'required',
	        ],
	        'category_id' => [ 
	        	'name' => 'category_id',      			   
	            'type' => 'select', 
	            'label' => 'Родительская категория',
	            'options' => [],
			],
	        'desc' => [
	        	'name' => 'desc',
	            'type' => 'textarea',  
	            'label' => 'Описание',
	            'field-save' => 'array',
	            'attr' => ['rows' => '8']
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
	            'height' => '500'
	        ],
			'url' => [
	            'name' => 'url',
	            'type' => 'text', 
	            'label' => 'URL',
	            'validate' => 'nullable|regex:/^([-a-z0-9\/\.]+)$/|unique:categories,url',
	            'desc'	=> 'Поле может содержать алфавитно-цифровой символ, тире или точку, так же можно состовлять путь из нескольких сегменов разделенных /, не используйте символ косой черты в начале или конце поля.'
	        ],
    		'seo' => [
    			'name' => 'seo',
    			'type' => 'group',
    			'load-from' => 'seo-fields',
    			'field-save' => 'array',
    		],
		],
		'edit' => [
			'main' => [
				'label' => 'Основные',
				'name' => 'main',
				'fields' => [ 'name', 'category_id', 'desc' ]
			],'post' => [
				'label' => 'Страница',
				'name' => 'post',
				'fields' => [ 'post_name', 'post_text' ],
			],'seo' => [
				'label' => 'SEO',
				'name' => 'seo',
				'fields' => [ 'url', 'seo' ],
			],
		],
		'sortable' => ['name'],
		'search' => [
			[
          		'name' => 'parent_cat',
          		'options-empty' => 'Все категории',
          		'field-from' => 'category_id',
          		'label' => '',
          		'fields' => [ 'category_id'],
          		'search-type' => 'filter',
          	],
        ],
	];