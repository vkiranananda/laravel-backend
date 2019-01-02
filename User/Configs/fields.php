<?php
	return [
		
        'list' => [
        	[ 'name' => 'id', 'label' => 'ID',  'attr' => [ 'width' => '30px' ], 'sortable' => true ],
        	[ 'name' => 'name', 'icon' => 'file', 'link' => 'edit', 'sortable' => true ],
        	[ 'name' => 'email', 'sortable' => true  ],
        	[ 'name' => 'updated_at', 'label' => 'Дата модификации', 'attr' => [ 'width' => '190px;' ], 'sortable' => true ]
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
	            'value' => 'user',
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
	            'validate' => 'required|email|unique:users,email',
	        ],
	        'photo' => [
	            'type' => 'gallery', 
	            'name' => 'photo', 
	            'label' => 'Фото',
	            'max-files' => 1,
	            'field-save' => 'array',
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
			'main' => [
				'label' => 'Основные',
				'name' => 'main',
				'fields' => [ 'name', 'photo', 'role', 'email', 'password', 'send_mail' ],
			],
		],
	];