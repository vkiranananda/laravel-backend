# Бэкенд для Laravel 

## Установка

1. Включаем кэширование memcached (можно и другой но с использованием тэгов)
2. Создаем доступ к бд.
3. Если нужно писать сессии в бд то `php artisan session:table`, и правим конфиг с сессиями 
4. Добавляем в filesystems новый диск
```
'uploads' => [
            'driver' => 'local',
            'root' => public_path().'/uploads',
            'visibility' => 'public',
        ],
```
5. Инсталим бэкенд `composer require vkiranananda/backend`
6. Заходим в каталог `vendor/vkiranananda/backend/install`, копируем каталог `backend` в корень проекта, `migrations` в миграции. Смотрим если какие то модули не нужны то не копируем к ним миграции, каталог `public` так же в корень проекта.
7. Для того что бы работал редактор в записях нужно получить tinymce, например так: `npm install tinymce   tinymce-i18n` и скопировать их в `public/backend/tinymce`, локализацию в каталог `public/backend/tinymce/langs`. Версия на которой тестировалось tinymce-5.1.1
8. В файле `backend/routes.php` можно временно отключить `'middleware' => ['auth.basic']` . После того как добавите первого пользователя нужно включить  авторизацию обратно. 
9. Добавляем в файл с маршрутами `routes/web.php` строку `Backend::installRoutes('Backend');`
10. Добавляем  в  файл `composer.json` в секцию `autoload -> psr-4` новое пространство имен `"Backend\\": "backend/"`
11. Инсталим миграции `php artisan migrate`
12. Добавляем алиас `'Categories' => Backend\Category\Facades\CategoriesFacade::class,`

### Сборка админки

В файл webpack.mix.js добавляем код

```mix.webpackConfig({
    devtool: "nosources-source-map"
});

mix.js('vendor/vkiranananda/backend/resources/js/backend.js', 'public/backend/js/admin.js').version();

mix.sass('vendor/vkiranananda/backend/resources/sass/backend.scss', 'public/backend/css/backend.css').options({
      processCssUrls: false
   }).version();
```

Устанавливаем зависимости.

`npm install jquery popper.js bootstrap lodash.clonedeep lodash.size vue vuex vue-multiselect vue-the-mask vue2-datepicker vuedraggable`


Остальное после :).
