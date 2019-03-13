<?php
	return [
		'fields' => [
			'title' => [		
	            'type' => 'text', 
	            'name' => 'title', 
	            'label' => 'Название сайта',
	            'field-save' => 'array',
	        ],
	        'description' => [		
	            'type' => 'textarea', 
	            'name' => 'description', 
	            'label' => 'Описание сайта', 
	            'field-save' => 'array',
	            'attr' => [ 'rows' => '3' ]
    		 ],
    		 'robots-index-deny' => [
	            'type' => 'radio', 
	            'name' => 'robots-index-deny', 
	            'label' => 'Запретить индексацию сайта в robots.txt',
	            'desc' => 'Если Да то, сайт индексироваться не будет',
	            'field-save' => 'array',
	            'value' => '1',
	            'options' => [
	            	['value' => '1', 'label' => 'Да'],
	            	['value' => '0', 'label' => 'Нет'],
	            ],
	        ],
	        'robots' => [		
	            'type' => 'textarea', 
	            'name' => 'robots', 
	            'label' => 'robots.txt',
	            'attr' => ['rows' => 15], 
	            'field-save' => 'array',
    		 ],
    		 'robots-sitemap' => [
	            'type' => 'radio', 
	            'name' => 'robots-sitemap', 
	            'label' => 'Включить карту сайта в robots.txt',
	            'field-save' => 'array',
	            'value' => '0',
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
				'fields' => [ 'title', 'description' ],
			],
			'seo' => [
				'label' => 'SEO',
				'name' => 'seo',
				'fields' => [ 'robots', 'robots-sitemap', 'robots-index-deny' ],
			],
		],
	];