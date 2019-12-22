<?php
	return [

        'list' => [
        	[ 'name' => 'name', 'icon' => 'file', 'link' => 'edit', 'sortable' => true ],
        	[ 'name' => 'url', 'attr' => [ 'width' => '10%' ] ],
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
	            'type' => 'editor', 
	            'label' => 'Текст',
	            'format' => 'fool',
	            'size' => 'normal',
	            'upload' => true,
	        ],
    		'seo' => [
    			'name' => 'seo',
    			'type' => 'group',
    			'load-from' => 'seo-fields',
    			'field-save' => 'array' 
    		],
			'url' => [
	            'name' => 'url',
	            'type' => 'text', 
	            'label' => 'URL',
	            'validate' => 'nullable|alpha_dash',
	        ], 
	    ],

		'edit' => [
			'default' => [
				'label' => 'Основные',
				'name' => 'default',
				'fields' => [ 'name','text' ],
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
          	]
        ]
    ];