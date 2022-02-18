<?php
	return [

        'list' => [
        	[ 'name' => 'name', 'icon' => 'file', 'link' => 'edit-show', 'sortable' => true ],
        	[ 'name' => 'category', 'label' => 'Категория', 'attr' => [ 'width' => '20%' ], 'func' => 'indexCategoryField', 'link' => 'category' ],
        	{url}[ 'name' => 'url', 'attr' => [ 'width' => '10%' ] ],
        	[ 'name' => 'updated_at', 'type' => 'date', 'time' => true, 'label' => 'Дата модификации', 'attr' => [ 'width' => '190px' ], 'sortable' => true ]
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
    		'category_id' => [
	        	'name' => 'category_id',
        		'type' => 'select',
        		'label' => 'Категория',
    		],
    		{seo}'seo' => [
    			{seo}'name' => 'seo',
    			{seo}'type' => 'group',
    			{seo}'load-from' => 'seo-fields',
    			{seo}'field-save' => 'array'
    		{seo}],
			{url}'url' => [
	            {url}'name' => 'url',
	            {url}'type' => 'text',
	            {url}'label' => 'URL',
	            {url}'validate' => 'nullable|alpha_dash',
	        {url}],
	    ],
	    {sort}'sortable' => ['name'],
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
			{urlseo}'seo' => [
				{urlseo}'label' => 'SEO',
				{urlseo}'name' => 'seo',
				{urlseo}'fields' => [
					{urlseo}{url}'url',
					{urlseo}{seo}'seo'
				{urlseo}],
			{urlseo}]
		],
		'search' => [
          	[
          		'name' => 'search',
          		'type' => 'text',
        		'attr' => [ 'placeholder' => 'Введите текст для поиска' ],
          		'fields' => [ 'name', 'text' ],
          	],[
          		'options-empty' => 'Все категории',
          		'name' => 'parent_cat',
          		'field-from' => 'category_id',
          		'label' => '',
          		'fields' => [ 'category_id'],
          		'search-type' => 'filter',
          	],
        ]
    ];
