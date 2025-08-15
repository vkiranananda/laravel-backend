<?php

return [
    [
        'label' => 'Сайт',
        'icon' => 'globe',
        'url' => url('/'),
    ],[
        'separator' => true,
        'space-top' => 2,
    ],[
        'label' => 'Админка главная',
        'icon' => 'home',
        'url' => action('\Backend\Home\Controllers\HomeController@index'),
    ],[
    ],[
        'separator' => true,
    ],[
        'type' => 'method',
        'name' => 'category',
        'method' => '\Backend\Root\Category\Services\Menu::getCats',
    ],[
        'separator' => true,
    ],[
        'label' => 'Менеджер файлов',
        'icon' => 'folder',
        'url' => action('\Backend\MediaFile\Controllers\FileManagerController@index'),
        'user-access-key' => 'FileManager',

    ],[
        'separator' => true,
    ],[
        'label' => 'Разделы',
        'icon' => 'list-unordered',
        'url' => action('\Backend\Category\Controllers\CategoryRootController@index'),
        'user-access-key' => 'Category',
    ],[
        'label' => 'Опции',
        'icon' => 'key',
        'url' => action('\Backend\Option\Controllers\OptionController@index'),
        'user-access-key' => 'Option',
    ],[
        'label' => 'Конструктор меню',
        'icon' => 'list-unordered',
        'url' => action('\Backend\MenuBuilder\Controllers\MenuBuilderController@index'),
        'user-access-key' => 'MenuBuilder',
    ],[
        'label' => 'Пользователи',
        'icon' => 'user',
        'url' => action('\Backend\User\Controllers\UserController@index'),
        'user-access-key' => 'User',
    ],[
        'label' => 'Роутинг',
        'icon' => 'terminal',
        'space-top' => 2,
        'url' => action('\Backend\Root\Category\Controllers\InfoController@routes'),
        'user-access-key' => 'CategoryRoutingRead',
    ],
];
