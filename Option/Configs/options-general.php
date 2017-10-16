<?php
	return [
		'lang' => [
			'title' => 'Настройки',
		],

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
    		 ],
    		 'robots-index-deny' => [
	            'type' => 'radio', 
	            'name' => 'robots-index-deny', 
	            'label' => 'Запретить индексацию са сайта в robots.txt',
	            'desc' => 'Если Да то, сайт индексироваться не будет',
	            'field-save' => 'array',
	            'value' => '1',
	            'options' => [
	            	['value' => '1', 'label' => 'Да'],
	            	['value' => '0', 'label' => 'Нет'],
	            ],
	        ],
	    ],
	

		'edit' => [
			'default' => [
				'tab_name' => 'Основные',
				'id' => 'main',
				'fields' => [
					[ 'name' => 'title' ],
					[ 'name' => 'description', 'attr' => [ 'rows' => '3' ] ],
					[ 'name' => 'robots-index-deny' ],
			    ],
			],
		],
	];