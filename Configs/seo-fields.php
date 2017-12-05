<?php
return [
	'edit' => [
        'seo' => [
    		'tab_name' => 'SEO',
    		'id' => 'seo',
    		'fields' => [
    			[ 'name' => 'url' ],
    			[ 'name' => 'seo_title' ],
    			[ 'name' => 'seo_description', 'attr' => ['rows' => '3'] ],
    			[ 'name' => 'seo_title_h1' ],
    		],
        ],
	],
	'fields' => [
		'url' => [
            'name' => 'url', 
            'type' => 'text', 
            'label' => 'URL',
            'validate' => 'nullable|alpha_dash',
        ],
        'seo_title' => [
            'name' => 'seo_title', 		
            'type' => 'text', 
            'label' => 'Title',
            'field-save' => 'array',
        ],
        'seo_description' => [	
            'name' => 'seo_description', 	
            'type' => 'textarea',  
            'label' => 'Description',
            'field-save' => 'array',
		 ],
	 	'seo_title_h1' => [
            'type' => 'radio', 
            'name' => 'seo_title_h1', 
            'field-save' => 'array',
            'label' => 'Заголовок &lt;H1&gt;',
            'desc' => 'Если установленов "Да", заголовок будет обернут тэгом &lt;H1&gt;',
            'value' => '1',
            'options' => [
            	['value' => '1', 'label' => 'Да'],
            	['value' => '0', 'label' => 'Нет'],
            ],
        ],
    ],
];