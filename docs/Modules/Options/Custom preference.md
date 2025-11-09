# Для создания отдельной страницы с настройками используйте такую схему
Создаем каталог для настроек в backend/Settings/ и в нем файлы:

**Controllers/SettingsController.php**

```php
<?php

namespace Backend\Settings\Controllers;

use Backend\Root\Option\Controllers\OptionResourcesController;

class SettingsController extends OptionResourcesController
{
  // Если нужно указать нестандартное расположение конфигов
  protected $configPath = 'MediaFile::settings-config';
  protected $fieldsPath = 'MediaFile::settings-fields';
  protected $configRoot = true;

  // Для подключения обработки прав доступа
  protected string $userAccessKey = 'MediaFile';
  use \Backend\Root\User\Services\UserAccessTrait;
}
```

**Configs/config.php**

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

**Configs/fields.php**

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