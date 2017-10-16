<?php
	return [
		'lang' => [
			'list-title' => 'Список пользователей',
			'create-title' => 'Создать пользователя',
			'edit-title' => 'Редактирование пользователя',
		],

        'list' => [
        	[ 'name' => 'id', 'label' => 'ID',  'attr' => [ 'width' => '30px' ] ],
        	[ 'name' => 'name', 'icon' => 'file', 'link' => 'edit'],
        	[ 'name' => 'email' ],
        	[ 'name' => 'updated_at', 'label' => 'Дата модификации', 'attr' => [ 'width' => '190px;' ] ]
        ],

		'fields' => [
	        'name' =>[
	            'type' => 'text', 
	            'name' => 'name', 
	            'label' => 'Имя пользователя',
	            'validate' => 'required',
	       	],
	       	'role' => [
	            'type' => 'select', 
	            'name' => 'role', 
	            'label' => 'Роль',
	            'desc' => 'Администратор - полные привелегии, Редактор - только редактирование, Пользователь - все то что для регистрированных пользователей.',
	            'options' => [
	            	['value' => 'user', 'label' => 'Пользователь'],
	            	['value' => 'admin', 'label' => 'Администратор'],
	            ],
	        ],
	        'email' => [
	            'type' => 'text', 
	            'name' => 'email', 
	            'label' => 'Email',
	            'desc' => 'Используется для входа',
	            'validate' => 'email|unique:users,email',
	        ],
	        'password' => [
	            'type' => 'text', 
	            'name' => 'password', 
	            'label' => 'Пароль',
	            'validate' => 'min:6',
	            'desc' => 'Минимальная длина пароля 6 символов',
	            'value' => '',
	       	],
   			'send_mail' => [ 'type' => 'radio', 
	            'name' => 'send_mail', 
	            'label' => 'Отправить письмо',
	            'desc' => 'Отправить письмо с данными пользователя на указанную почту.',
	            'value' => 'no',
	            'field-save' => 'none',
	            'options' => [
	            	['value' => 'yes', 'label' => 'Да'],
	            	['value' => 'no', 'label' => 'Нет'],
	            ],
	        ],
		],

		'edit' => [
			'default' => [
				'tab_name' => 'Основные',
				'id' => 'main',
				'fields' => [
			        [ 'name' => 'name' ],
			        [ 'name' => 'role' ],
			        [ 'name' => 'email' ],
			        [ 'name' => 'password' ],
			        [ 'name' => 'send_mail' ],
			    ],
			],
		],
	];