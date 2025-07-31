# Библиотека иконок

## Обзор

В проекте используется единая библиотека SVG иконок, реализованная через CSS-классы. Все иконки находятся в файле `vendor/vkiranananda/backend/resources/sass/icons.scss`.

## Использование

### Базовый синтаксис

```html
<div class="icon icon-[название-иконки] icon-[размер]"></div>
```

### Размеры иконок

- `icon-1` - 1rem (16px)
- `icon-2` - 1.5rem (24px) 
- `icon-3` - 2rem (32px)
- `icon-4` - 2.5rem (40px)
- `icon-5` - 3rem (48px)

### Цветовые варианты

- `icon-primary` - синий (#0d6efd)
- `icon-secondary` - серый (#6c757d)
- `icon-success` - зеленый (#22c55e)
- `icon-danger` - красный (#ef4444)
- `icon-warning` - оранжевый (#f59e0b)
- `icon-info` - голубой (#3b82f6)
- `icon-light` - светлый (#f8f9fa)
- `icon-dark` - темный (#212529)

## Доступные иконки

### Файловый менеджер
- `icon-upload-cloud` - облако загрузки
- `icon-folder` - папка (золотистая)
- `icon-file` - файл (синий)
- `icon-close` - крестик
- `icon-check` - галочка
- `icon-spinner` - анимированный спиннер

### Навигация
- `icon-home` - домой
- `icon-settings` - настройки
- `icon-user` - пользователь
- `icon-menu` - меню (гамбургер)

### Действия
- `icon-edit` - редактировать
- `icon-delete` - удалить
- `icon-plus` - добавить
- `icon-search` - поиск

### Статусы
- `icon-success` - успех (зеленая галочка)
- `icon-error` - ошибка (красный крестик)
- `icon-warning` - предупреждение (оранжевый треугольник)
- `icon-info` - информация (синий круг)

## Примеры использования

### Простая иконка
```html
<div class="icon icon-home icon-3"></div>
```

### Иконка с цветом
```html
<div class="icon icon-success icon-2 icon-success"></div>
```

### Иконка с hover эффектом
```html
<div class="icon icon-edit icon-3 icon-hover"></div>
```

### В кнопке
```html
<button class="btn btn-primary">
    <div class="icon icon-plus icon-1 me-2"></div>
    Добавить
</button>
```

### В списке
```html
<div class="d-flex align-items-center">
    <div class="icon icon-folder icon-2 me-3"></div>
    <span>Название папки</span>
</div>
```

## Добавление новых иконок

### 1. Подготовка SVG
- Используйте viewBox="0 0 24 24"
- Уберите лишние атрибуты
- Используйте `currentColor` для цвета

### 2. Кодирование в base64
```bash
# В терминале
echo '<svg>...</svg>' | base64
```

### 3. Добавление в SCSS
```scss
.icon-new-icon {
  background-image: url("data:image/svg+xml;base64,КОДИРОВАННЫЙ_SVG");
  background-repeat: no-repeat;
  background-position: center;
  background-size: contain;
}
```

### 4. Документирование
Добавьте описание иконки в этот файл.

## Анимации

### Спиннер
```html
<div class="icon icon-spinner icon-3"></div>
```

### Hover эффекты
```html
<div class="icon icon-edit icon-3 icon-hover"></div>
```

## Адаптивность

Иконки автоматически масштабируются на мобильных устройствах:
- На экранах < 768px размеры уменьшаются на 12.5%

## Лучшие практики

1. **Используйте семантические названия** - `icon-user` вместо `icon-person`
2. **Выбирайте подходящий размер** - не используйте `icon-5` для мелких элементов
3. **Добавляйте цвета контекстно** - `icon-success` для успешных операций
4. **Используйте hover эффекты** для интерактивных элементов
5. **Группируйте иконки** в документации по категориям

## Совместимость

- Поддерживаются все современные браузеры
- Fallback для старых браузеров через background-image
- Работает с Bootstrap 5
- Поддерживает темные темы через `currentColor`

## Подключение

Файл иконок автоматически подключается через Vite в `backend.scss`:

```scss
@import 'icons';
``` 