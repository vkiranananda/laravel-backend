<?php
	return array_merge_recursive([
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
	        'announcement' => [
	        	'name' => 'announcement',	
	            'type' => 'textarea', 
	            'label' => 'Анонс',
	            'rows' => '5',
	            'field-save' => 'array',
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
    		'sort_num' => [	
    			'name' => 'sort_num',	
	            'type' => 'text', 
	            'label' => 'Номер сортировки',
	            'validate' => 'integer',
	            'value' => '0',
	        ],
	        'gallery' => [	
	        	'name' => 'gallery',	
	            'type' => 'gallery', 
	            'label' => 'Галерея',
        		'field-save' => 'array',
	        ],
			'region' => [ 
				'name' => 'region',
        		'type' => 'select', 
        		'label' => 'Регион', 
        		'field-save' => 'relation',
        		'options' => [
        			[ 'value' => '', 'label' => '' ],
	         		[ 'value' => 'altajskij-kraj', 'label' => 'Алтайский край' ],
	         		[ 'value' => 'respublika-altaj', 'label' => 'Республика Алтай' ],
        		],
    		],
    		'area' => [ 
    			'name' => 'area', 
        		'type' => 'select', 
        		'label' => 'Район', 
        		'field-save' => 'relation',
        		'options' => [
        			[ 'value' => '', 'label' => '', 'data-region' => ''],
					[ 'value' => 'altajskij', 'label' => 'алтайский', 'data-region' => 'altajskij-kraj'],
					[ 'value' => 'charyshskij', 'label' => 'чарышский' , 'data-region' => 'altajskij-kraj'],
					[ 'value' => 'smolenskij', 'label' => 'смоленский', 'data-region' => 'altajskij-kraj'],
					[ 'value' => 'soloneshenskij', 'label' => 'солонешенский' , 'data-region' => 'altajskij-kraj'],
					[ 'value' => 'sovetskij', 'label' => 'советский' , 'data-region' => 'altajskij-kraj'],
					[ 'value' => 'bijskij', 'label' => 'бийский' , 'data-region' => 'altajskij-kraj'],
					[ 'value' => 'zmeinogorskij', 'label' => 'змеиногорский' , 'data-region' => 'altajskij-kraj'],
					[ 'value' => 'kurinskij', 'label' => 'курьинский' , 'data-region' => 'altajskij-kraj'],
					[ 'value' => 'krasnoshhekovskij', 'label' => 'краснощековский', 'data-region' => 'altajskij-kraj'],
					[ 'value' => 'zavjalovskij', 'label' => 'завьяловский', 'data-region' => 'altajskij-kraj'],
					[ 'value' => 'slavgorodskij', 'label' => 'славгородский', 'data-region' => 'altajskij-kraj'],
					[ 'value' => 'zarinskij', 'label' => 'заринский', 'data-region' => 'altajskij-kraj'],
					[ 'value' => 'krasnogorskij', 'label' => 'красногорский', 'data-region' => 'altajskij-kraj'],
					[ 'value' => 'troickij', 'label' => 'троицкий', 'data-region' => 'altajskij-kraj'],
					[ 'value' => 'kosihinskij', 'label' => 'косихинский', 'data-region' => 'altajskij-kraj'],
					[ 'value' => 'bystroistokskij', 'label' => 'быстроистокский', 'data-region' => 'altajskij-kraj'],


					[ 'value' => 'majminskij', 'label' => 'майминский' , 'data-region' => 'respublika-altaj'],
					[ 'value' => 'chojskij', 'label' => 'чойский', 'data-region' => 'respublika-altaj'],
					[ 'value' => 'turochakskij', 'label' => 'турочакский', 'data-region' => 'respublika-altaj'],
					[ 'value' => 'chemalskij', 'label' => 'чемальский', 'data-region' => 'respublika-altaj'],
					[ 'value' => 'shebalinskij', 'label' => 'шебалинский', 'data-region' => 'respublika-altaj'],
					[ 'value' => 'ongudajskij', 'label' => 'онгудайский', 'data-region' => 'respublika-altaj'],
					[ 'value' => 'ust-kanskij', 'label' => 'усть-канский', 'data-region' => 'respublika-altaj'],
					[ 'value' => 'ust-koksinskij', 'label' => 'усть-коксинский', 'data-region' => 'respublika-altaj'],
					[ 'value' => 'ulaganskij rajon', 'label' => 'улаганский район', 'data-region' => 'respublika-altaj'],
					[ 'value' => 'kosh-agachskij', 'label' => 'кош-агачский', 'data-region' => 'respublika-altaj']
        		],
    		],	
    	],
		'edit' => [
			'default' => [
				'tab_name' => 'Основные',
				'id' => 'main',
				'fields' => [
			        [ 'name' => 'name' ],
			        [ 'name' => 'announcement', 'attr' => [ 'rows' => '5' ] ],
			        [ 'name' => 'text', 'attr' => [ 'rows' => '25' ] ],
			    ],
			],'attr' => [
				'tab_name' => 'Атрибуты',
				'id' => 'attr',
				'fields' => [
			        [ 'name' => 'category_id' ],
			        [ 'name' => 'sort_num' ],
			        [ 'name' => 'gallery' ],
			    ],
			],
			'dop' => [
				'tab_name' => 'Район',
				'id' => 'rayon',
				'fields' =>[
					[ 'name' => 'region', 'attr' => ['id' => 'post-select-region']	],
					[ 'name' => 'area' ],	
				],
			],
		],
	], BackendConfig::get('seo-fields') 
);