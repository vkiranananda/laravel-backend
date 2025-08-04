# Выбор файлов:

**max-files** - количество разрешенных файлов
**file-type** - если `image`, то файлы будут выводиться сеткой и добавить можно
только картинк. Иначе список будет списком.
**upload-button** - если `false`, то будет скрыта кнопка загрузки.
**upload-url** - кастомный url для загрузки файла.
**insert-button** - если `false`, то будет скрыта кнопка вставки.
**upload-url** - Url для загрузки файла.

```
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
