<?php
return [
    'list' => [
        ['name' => 'name', 'icon' => 'file', 'link' => 'edit', 'sortable' => true],
        ['name' => 'category', 'label' => 'Категория', 'attr' => ['width' => '20%'], 'func' => 'indexCategoryField', 'link' => 'category'],
        ['name' => 'url', 'attr' => ['width' => '10%']],
        ['name' => 'updated_at', 'type' => 'date', 'time' => true, 'label' => 'Дата модификации', 'type' => 'date', 'time' => true, 'attr' => ['width' => '190px'], 'sortable' => true]
    ],
    'fields' => [
        'name' => [
            'name' => 'name',
            'type' => 'text',
            'label' => 'Название',
            'validate' => 'required',
        ],
        'category_id' => [
            'name' => 'category_id',
            'type' => 'select',
            'label' => 'Родительская категория',
            'options' => [],
        ],
        'desc' => [
            'name' => 'desc',
            'type' => 'textarea',
            'label' => 'Описание',
            'field-save' => 'array',
            'attr' => ['rows' => '8']
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
            'desc' => 'Поле может содержать алфавитно-цифровой символ, тире или точку, так же можно состовлять путь из нескольких сегменов разделенных /, не используйте символ косой черты в начале или конце поля.'
        ],
        'seo' => [
            'name' => 'seo',
            'type' => 'group',
            'load-from' => 'seo-fields',
            'field-save' => 'array',
        ],
        'sitemap' => [
            'name' => 'sitemap',
            'type' => 'group',
            'load-from' => 'Sitemap::category',
            'field-save' => 'array',
            'label' => 'Карта сайта'
        ],
    ],
    'edit' => [
        'main' => [
            'label' => 'Основные',
            'name' => 'main',
            'fields' => ['name', 'category_id', 'desc']
        ], 'post' => [
            'label' => 'Страница',
            'name' => 'post',
            'fields' => ['post_name', 'post_text'],
        ], 'seo' => [
            'label' => 'SEO',
            'name' => 'seo',
            'fields' => ['url', 'seo'],
        ], 'sitemap' => [
            'label' => 'Карта сайта',
            'name' => 'sitemap',
            'fields' => ['sitemap'],
        ]
    ],
    'sortable' => ['name'],
    'search' => [
        [
            'name' => 'parent_cat',
            'options-empty' => 'Все категории',
            'field-from' => 'category_id',
            'label' => '',
            'fields' => ['category_id'],
            'search-type' => 'filter',
        ],
    ],
];
