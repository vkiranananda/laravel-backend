<?php
	$sitemapFields = GetConfig::backend('Sitemap::fields', true)['fields'];
	unset($sitemapFields['sitemap-changefreq']['conf-field-save'],$sitemapFields['sitemap-priority']['conf-field-save']);
	$sitemapFields['sitemap-changefreq']['name'] = 'changefreq';
	$sitemapFields['sitemap-priority']['name'] = 'priority';

	return [
		'lang' => [
			'list-title' => 'Список ссылок',
			'create-title' => 'Добавить ссылку',
			'edit-title' => 'Редактировать ссылку',
		],
		'tabs' => [
			'default' => [
				'tab_name' => 'Основные',
				'id' => 'main',
				'fields' => [
			        [
			            'type' => 'text', 
			            'name' => 'name', 
			            'label' => 'Название',
			            'conf-list' => 'edit',
			            'conf-list-icon' => 'file',
			        ],[	
						'type' => 'text', 
			            'name' => 'url', 
			            'conf-list' => '1',
			            'conf-validate' => 'required',
			            'desk' => 'Указывается относительный урл от корня сайта',
			            'label' => 'Ссылка',
			        ],
			        'sitemap-changefreq' => $sitemapFields['sitemap-changefreq'],
			        'sitemap-priority' => $sitemapFields['sitemap-priority'],
			    ],
			],
		],
	];