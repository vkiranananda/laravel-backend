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
    ],
];