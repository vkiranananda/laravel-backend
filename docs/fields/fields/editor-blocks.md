# Пример
```
'text' => [
    'name' => 'text',
    'type' => 'editor-blocks',
    'label' => 'Текст',
    'plugins' => [
        "image" => [
            'text-align' => true,
        ],
        "styled-text" =>
            [
                'label' => 'Стилизованный текст',
                'text-align' => true,
                'styles' => [
                    ["className" => 'text-style-1', "label" => 'Стиль 1'],
                    ["className" => 'text-style-2', "label" => 'Стиль 2'],
                ]
            ],
        "delimiter" => true,
        "quote" => true,
        "table" => true,
        "raw" => true,
    ],
```
# Опции
`plugins` - сипсок плагинов

# Плагины
`image` - вставка картинки
`styled-text` - стилизованный текст, в настройках указывается класс со стилем
`delimiter` - разделители, так же в настройках можно добавить свои разделители 
к тем что уже имеются.
`quote` - цитаты
`table` - таблицы
`raw` - html код

## Опции плагинов
Для каждого плагина можно добавить опции:
`label` - название в списке
`text-align` - выравнивание текста
`toolbar` - форматирование текста.
