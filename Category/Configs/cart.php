<?php
	return [
		'lang' => [
			'list-title' => 'Корзина',
		],
        'conf' => [
	        'list-no-actions' => true,
        ],
		'list-fields' => [
        [
            'type' => 'text', 
            'name' => 'name', 
            'label' => 'Название',
            'conf-list' => '',
            'conf-list-html' => '',
       	],[
            'type' => 'text', 
            'name' => 'deleted_at', 
            'label' => 'Дата удаления',
            'conf-list' => '',
            'conf-list-td-attr' => 'width="180px"',
       	],[
            'type' => 'text', 
            'name' => 'controls', 
            'label' => '',
            'conf-list' => '',
            // 'conf-list-td-attr' => 'width="200px"',
       	],
    ],
];