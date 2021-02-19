<?php
return [
    'list' => [
        [ 'name' => 'name', 'icon' => 'file', 'link' => 'edit-show', 'sortable' => true ],
        [ 'name' => 'tree', 'label' => '', 'link' => 'category', 'func' => 'indexTreeField', 'attr' => [ 'width' => '50px' ] ],
        [ 'name' => 'url', 'attr' => [ 'width' => '50px' ], 'sortable' => true ],
        [ 'name' => 'mod', 'attr' => [ 'width' => '50px' ], 'sortable' => true ],
        [ 'name' => 'conf-type', 'attr' => [ 'width' => '50px' ]]
    ],
    'fields' => [
        'name' => [
            'name' => 'name',
            'type' => 'text',
            'label' => 'Название',
            'validate' => 'required',
        ],
        'mod' =>[
            'name' => 'mod',
            'type' => 'select',
            'label' => 'Модуль',
        ],
        'conf-type' => [
            'name' => 'conf-type',
            'type' => 'select',
            'field-save' => 'array',
            'label' => 'Структура',
            'options' => [
                [ 'value' => '', 'label' => 'Обычная' ],
                [ 'value' => 'hierarchical', 'label' => 'Древовидная' ],
            ],
        ],
        'conf-icon' => [
            'name' => 'conf-icon',
            'type' => 'input',
            'field-save' => 'array',
            'label' => 'Иконка в списке',
            'field-save' => 'array',
            'desc' => 'Иконки можно посмотреть <a href="https://octicons.github.com"target="_blank">тут</a> пишем без .octicon-'
        ],
        'desc' => [
            'name' => 'desc',
            'type' => 'textarea',
            'label' => 'Описание',
            'field-save' => 'array',
            'attr' => ['rows' => '8']
        ],
        'conf-count-posts' => [
            'name' => 'conf-count-posts',
            'type' => 'text',
            'label' => 'Количество записей на странице',
            'value' => '30',
            'desc' > '0 вывести все записи',
            'field-save' => 'array',
            'validate' => 'integer|required',
        ],
        'lang-list-title' => [
            'name' => 'lang-list-title',
            'type' => 'text',
            'label' => 'Заголовок в списке',
            'value' => '',
            'field-save' => 'array',
        ],
        'lang-create-title' => [
            'name' => 'lang-create-title',
            'type' => 'text',
            'label' => 'Новая запись',
            'value' => '',
            'field-save' => 'array',
        ],
        'lang-edit-title' => [
            'name' => 'lang-edit-title',
            'type' => 'text',
            'label' => 'Редактировать запись',
            'value' => '',
            'field-save' => 'array',
        ],
        'post_name' => [
            'name' => 'post_name',
            'type' => 'text',
            'label' => 'Заголовок',
        ],
        'post_text' => [
            'name' => 'post_text',
            'type' => 'editor',
            'label' => 'Текст',
            'format' => 'fool',
            'size' => 'normal',
            'upload' => true,
        ],
        'url' => [
            'name' => 'url',
            'type' => 'text',
            'label' => 'URL',
            'validate' => 'nullable|regex:/^([-a-z0-9\/\.]+)$/',
            'desc'	=> 'Поле может содержать алфавитно-цифровой символ, тире или точку, так же можно состовлять путь из нескольких сегменов разделенных /, не используйте символ косой черты в начале или конце поля.'
        ],
        'seo' => [
            'name' => 'seo',
            'type' => 'group',
            'load-from' => 'seo-fields',
            'field-save' => 'array'
        ],
        'sitemap' => [
            'name' => 'sitemap',
            'type' => 'group',
            'load-from' => 'Sitemap::category',
            'field-save' => 'array',
            'label' => 'Карта сайта'
        ],
    ],
    'sortable' => ['name'],
    'edit' => [
        'main' => [
            'label' => 'Основные',
            'name' => 'main',
            'fields' => [ 'name', 'mod', 'conf-type', 'conf-icon', 'desc' ]
        ],'conf' => [
            'label' => 'Настройки',
            'name' => 'conf',
            'fields' => [ 'conf-count-posts', 'lang-list-title', 'lang-create-title', 'lang-edit-title' ]
        ],'post' => [
            'label' => 'Страница',
            'name' => 'post',
            'fields' => [ 'post_name', 'post_text' ],
        ],'seo' => [
            'label' => 'SEO',
            'name' => 'seo',
            'fields' => [ 'url', 'seo' ],
        ],'sitemap' => [
            'label' => 'Карта сайта',
            'name' => 'sitemap',
            'fields' => [ 'sitemap' ],
        ]
    ],
];
