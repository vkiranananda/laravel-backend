# Для создания отдельной страницы с настройками используйте такую схему

Создаем каталог для настроек в backend/Settings/ и в нем файлы:

**Controllers/SettingsController.php**

```php
<?php

namespace Backend\Settings\Controllers;

use Backend\Root\Option\Controllers\OptionResourcesController;

class SettingsController extends OptionResourcesController
{
  // Для подключения обработки прав доступа
  protected string $userAccessKey = 'MediaFile';
  use \Backend\Root\User\Services\UserAccessTrait;

  // Если нужно указать нестандартное расположение конфигов
  protected $configPath = 'MediaFile::config-settings';
  protected $fieldsPath = 'MediaFile::fields-settings';
}
```

**Configs/config-settings.php**

```php
<?php
return [
    'lang' => [
        'edit-title' => 'Кастомные настройки'
    ],
    'options' => [
        // Уникальный ключ для доступа к настройкам.
        'name' => '_settings',
    ],
    'edit' => [
        'buttons' => [
            'save' => [
                'default' => 'save',
                'label' => 'Сохранить',
                'type' => 'primary'
            ],

        ]
    ],
];

```

**Configs/fields-settings.php**

```php
<?php
return [
    'fields' => [
        'my-setting' => [
            'name' => 'my-setting',
            'type' => 'text',
            'label' => 'Настройка',
            'field-save' => 'array',
        ],
    ],
    'edit' => [
        'default' => [
            'label' => 'Основные',
            'name' => 'default',
            'fields' => ['my-setting'],
        ]
    ]
];

```

**Добавляем роуты**

```php
// Устанавливаем роуты. $path - url, $class - класс контроллера, $routeName - название роута

Option::installRoutes('settings', '\Backend\Settings\Controllers\SettingsController', 'settings');
```

**Добавляем в меню**

```php
    [
        'label' => 'Настройки',
        'icon' => 'key',
        'url' => action('\Backend\Settings\Controllers\SettingsController@edit'),
        'user-access-key' => 'Settings',
    ],
```
