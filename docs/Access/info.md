## Меню

Добавляем ключ в меню со значением равным ключу модуля.

```php
'user-access-key' => 'ModuleKey',
```

## В контроллере

```php
    use \Backend\Root\User\Services\UserAccessTrait;

    protected string $userAccessKey = 'ModuleKey';
```

## Функция проверки доступа

Параметры:

$access - тип доступа edit-all, edit-owner, read-all, read-owner, create, destroy-all, destroy-owner
$modKey - Ключ модуля по которому будем сверть с ролью
$userId - Если указан будет учавствовать в типах read-owner, edit-owner, delete-owner,
если не указан вернет true

Возвращает:

bool - Вернет true или false.

```php
    use Backend\Root\User\Services\UserAccess;

    UserAccess::checkAccess('read-all', 'ModuleKey');
```
