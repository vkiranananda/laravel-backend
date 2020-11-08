<?php
return [

    'list' => [
        ['name' => 'id', 'label' => 'ID', 'attr' => ['width' => '30px'], 'sortable' => true],
        ['name' => 'name', 'icon' => 'file', 'link' => 'edit', 'sortable' => true],
    ],

    'fields-for-clone' => [
        'type' => 'group',
        'field-save' => 'array',
        'fields' => [
            'title' => [
                'type' => 'html',
                'html' => ''
            ],
            'read' => [
                'name' => 'read',
                'type' => 'select',
                'label' => 'Чтение',
                'col-classes' => 'col-3',
                'value' => 'no',
                'options' => [
                    'none' => ['value' => 'no', 'label' => 'Нет доступа'],
                    'all' => ['value' => 'all', 'label' => 'Все'],
                    'owner' => ['value' => 'owner', 'label' => 'Только свои'],
                ],
            ],
            'create' => [
                'name' => 'create',
                'type' => 'select',
                'label' => 'Создание',
                'col-classes' => 'col-3',
                'value' => 'no',
                'options' => [
                    'none' => ['value' => 'no', 'label' => 'Нет доступа'],
                    'all' => ['value' => 'all', 'label' => 'Разрешено'],
                ],
                'show' => [
                    [ 'type' => '==', 'field' => 'read', 'value' => 'all' ],
                    [ 'operator' => '||', 'type' => '==', 'field' => 'read', 'value' => 'owner' ]
                ]
            ],
            'edit' => [
                'name' => 'edit',
                'type' => 'select',
                'label' => 'Редактирование',
                'col-classes' => 'col-3',
                'value' => 'no',
                'options' => [
                    'none' => ['value' => 'no', 'label' => 'Нет доступа'],
                    'all' => ['value' => 'all', 'label' => 'Все'],
                    'owner' => ['value' => 'owner', 'label' => 'Только свои'],
                ],
                'show' => [
                    [ 'type' => '==', 'field' => 'read', 'value' => 'all' ],
                    [ 'operator' => '||', 'type' => '==', 'field' => 'read', 'value' => 'owner' ]
                ]
            ],
            'destroy' => [
                'name' => 'destroy',
                'type' => 'select',
                'label' => 'Удаление',
                'col-classes' => 'col-3',
                'value' => 'no',
                'options' => [
                    'none' => ['value' => 'no', 'label' => 'Нет доступа'],
                    'all' => ['value' => 'all', 'label' => 'Все'],
                    'owner' => ['value' => 'owner', 'label' => 'Только свои'],
                ],
                'show' => [
                    [ 'type' => '==', 'field' => 'read', 'value' => 'all' ],
                    [ 'operator' => '||', 'type' => '==', 'field' => 'read', 'value' => 'owner' ]
                ]
            ],
        ]
    ],

    'sortable' => ['name'],

    'fields' => [
        'name' => [
            'type' => 'text',
            'name' => 'name',
            'label' => 'Название',
            'validate' => 'required',
        ],
        'permissions' => [
            'name' => 'permissions',
            'type' => 'group',
            'field-save' => 'array',
            'fields' => []
        ]

    ],

    'edit' => [
        'main' => [
            'label' => 'Основные',
            'name' => 'main',
            'fields' => ['name', 'permissions'],
        ],
    ],
];
