# Ajax выпадающий список с поиском

## Описание

Поле для выбора одного или нескольких значений из списка, которое загружается по ajax с поиском.

## Параметры

- `value` - значение по умолчанию
- `options` - массив опций, по умолчанию [], опции можно не указывать если еще не выбрано значение, если значение выбрано, то нужно указать опции для этого значения, что бы они отображались в поле.
- `url` - url для загрузки опций, нужно вернуть массив опций как в примере ниже

# Пример

```php
  'select-field' => [
    'name' => 'select-field',
    'type' => 'select-search',
    'label' => 'Ajax выпадающий список',
    'value' => 0,
    'url' => action('\Backend\Contract\Controllers\ContractController@searchCounterparty'),
    'options' => [
      [
        'value' => 1,
        'label' => 'Да',
      ],
      [
        'value' => 0,
        'label' => 'Нет',
      ],
    ],
  ],
```

# Пример контроллера

```php
  // Получение опций по ajax для выпадающего списка
  public function searchCounterparty()
  {
    $res = [];
    $search = Request::input('value', '');
    if ($search == '')
      return $res;

    $counterparties = Counterparty::whereLike('name', '%' . $search . '%')->get(['name', 'id']);

    foreach ($counterparties as $counterparty) {
      $res[] = [
        'value' => $counterparty['id'],
        'label' => $counterparty['name']
      ];
    }

    return $res;
  }


  // При сохранении записи проверяем существование контрагента
  protected function resourceCombine($type)
  {
    if ($type == 'store' || $type == 'update') {
      $cpId = Request::input('fields.counterparty_id', '');
      if ($cpId != '' && !Counterparty::where('id', $cpId)->exists()) {
        abort(404, 'Контрагент не найден');
      }
    }
  }

  // При редактировании записи загружаем опции для выпадающего списка
  public function edit($id)
  {
    $this->getPost($id, 'edit-owner');

    $cp = Counterparty::where('id', $this->post['counterparty_id'])->first(['name']);

    $this->fields['fields']['counterparty_id']['options'] = [
      [
        'value' => $this->post['counterparty_id'],
        'label' => $cp->name
      ]
    ];

    return parent::edit($id);
  }
```
