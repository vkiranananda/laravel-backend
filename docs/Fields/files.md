# Выбор файлов

## Описание

Поле для выбора файлов.

## Параметры

- `max-files` - количество разрешенных файлов
- `file-type` - тип файла (image, file), по умолчанию file
- `upload-button` - если `false`, то будет скрыта кнопка загрузки, по умолчанию true
- `insert-button` - если `false`, то будет скрыта кнопка вставки, по умолчанию true
- `upload-url` - Url для загрузки файла, по умолчанию `/admin/media-file/upload`

## Примеры

```php
'files' => [
    'name' => 'files',
    'type' => 'files',
    'label' => 'Файлы',
    'field-save' => 'array',
    'max-files' => 5,
],
'gallery' => [
    'name' => 'gallery',
    'type' => 'files',
    'label' => 'Галерея',
    'field-save' => 'array',
    'file-type' => 'image',
],

```
