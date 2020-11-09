<?php
	return [

        'list' => [
        	[ 'name' => 'name', 'icon' => 'file', 'link' => 'edit-show', 'sortable' => true ],
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
          	]
        ]
    ];
