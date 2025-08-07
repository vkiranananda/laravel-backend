<?php
return [
  'fields' => [
    'enable-check-access' => [
      'name' => 'enable-check-access',
      'type' => 'select',
      'label' => 'Включить проверку доступа',
      'field-save' => 'array',
      'value' => 0,
      'col-classes' => 'col-4',
      'options' => [
        [
          'value' => 1,
          'label' => 'Да',
        ],
        [
          'value' => 0,
          'label' => 'Нет',
        ],
      ],
    ],
    'policy' => [
      'name' => 'policy',
      'type' => 'repeated',
      'label' => 'Правила',
      'field-save' => 'array',
      'add-label' => 'Добавить правило',
      'show' => [
        ['type' => '==', 'field' => 'enable-check-access', 'value' => 1],
      ],
      'fields' => [
        'user' => [
          'name' => 'user',
          'type' => 'select',
          'label' => 'Пользователь',
          'value' => 0,
          'col-classes' => 'col-5',
          'options' => [
            [
              'value' => 0,
              'label' => 'Все пользователи',
            ],
          ],
        ],
        'folder' => [
          'name' => 'folder',
          'type' => 'select',
          'label' => 'Папка',
          'value' => 0,
          'col-classes' => 'col-5',
          'options' => [
            [
              'value' => 0,
              'label' => 'Все папки',
            ],
          ],
        ],
        'permission' => [
          'name' => 'permission',
          'type' => 'select',
          'label' => 'Права доступа',
          'value' => 0,
          'col-classes' => 'col-2',
          'options' => [
            [
              'value' => 'read',
              'label' => 'Чтение',
            ],
            [
              'value' => 'write',
              'label' => 'Запись',
            ]
          ],
        ],
      ],
    ],
  ],
  'edit' => [
    'default' => [
      'label' => 'Основные',
      'name' => 'default',
      'fields' => ['enable-check-access', 'policy'],
    ]
  ]
];
