<?php
return [

    'list' => [
        ['name' => 'id', 'label' => 'ID', 'attr' => ['width' => '30px'], 'sortable' => true],
        ['name' => 'name', 'icon' => 'file', 'link' => 'edit', 'sortable' => true],
        ['name' => 'email', 'sortable' => true],
        ['name' => 'updated_at', 'type' => 'date', 'time' => true, 'label' => 'Дата модификации', 'attr' => ['width' => '190px;'], 'sortable' => true]
    ],

    'fields' => [
        'name' => [
            'type' => 'text',
            'name' => 'name',
            'label' => 'Имя пользователя',
            'validate' => 'required',
        ],
        'email' => [
            'type' => 'text',
            'name' => 'email',
            'label' => 'Email',
            'desc' => 'Используется для входа',
            'validate' => 'required|email|unique:users,email',
        ],
        'password' => [
            'type' => 'text',
            'name' => 'password',
            'label' => 'Пароль',
            'validate' => 'min:6',
            'desc' => 'Минимальная длина пароля 6 символов',
            'value' => '',
        ],
        'send_mail' => ['type' => 'radio',
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
            'fields' => ['name', 'email', 'password', 'send_mail'],
        ],
    ],
];
