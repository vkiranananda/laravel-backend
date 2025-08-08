# Редактор

## Описание

Редактор для текста.

## Параметры

- `format` - формат редактора (full, small, normal), по умолчанию normal
- `upload` - разрешить загрузку файлов (true, false), по умолчанию true

## Примеры

```php
        'editor' => [
            'type' => 'editor',
            'name' => 'editor',
            'label' => 'Текст',
            'field-save' => 'array',
            'format' => 'full', // full, small, normal
            'upload' => true, // true, false
        ],
```

