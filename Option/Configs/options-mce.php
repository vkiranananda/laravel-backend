<?php
	return [
	    'conf' => [
	        'load-scripts' => [
        		"/js/tinymce/tinymce.min.js",
        	],
        ],
		'fields' => [
			'value' => [	
		        'type' => 'mce', 
		        'name' => 'value', 
		        'label' => 'Текст',
		        'attr' => [ 'rows' => '25' ],
		        'upload' => true,
	        ],
		],
	];