<?php

namespace Backend\Root\Form\Services\Traits;

use Auth;
use Helpers;
use Illuminate\Database\Eloquent\Model;
use Request;

trait Index
{

    // !Ввод списка записей
    public function index()
    {
        // Проверям права доступа на полный список
        if (!$this->getUserAccess('read-all')) {
            // Если нет проверяем на владельца и делаем выборку.
            if ($this->getUserAccess('read-owner')) {
                // Если в конфиге стоит опция user-id делаем выборку по этому полю, иначе выводим все записи.
                if (isset($this->config['user-id']) && $this->config['user-id']) $this->post = $this->post->where('user_id', Auth::user()->id);
            } // Иначе выходим.
            else abort(403, 'Access deny!');
        }
        $this->resourceCombine('index');

        //Поиск
        $this->indexSearch();
        //Сортировка
        $this->indexOrder();

        //Параметры к урлу
        $urlPostfix = "";

        // Получаем все дополнительные параметры.
        foreach ($this->config['url-params'] as $param) {
            $urlPostfix = Helpers::mergeUrlParams($urlPostfix, $param, Request::input($param, ''));
        }

        $this->dataReturn['config']['urlPostfix'] = $urlPostfix;

        //------------------------------Кнопка Создать----------------------------------------

        $this->dataReturn['config']['menu'] = $this->indexListMenu($urlPostfix);

        // -----------------------------Хлебные крошки----------------------------------------

        $this->dataReturn['breadcrumbs'] = $this->indexBreadcrumbs($urlPostfix);

        // --------------------------------Компоненты------------------------------------------

        $this->dataReturn['components'] = $this->indexComponents();

        //-------------------------------Подготавливаем поля----------------------------------

        $fields = [];
        $fields_prep = []; // Методы доп обработки values
        $optionFields = []; // Поля имеющие option

        // Меню для элемента списка
        $this->dataReturn['itemMenu'] = $this->indexItemMenu();


        foreach ($this->fields['list'] as $field) {

            // Получаем базовое поле. ВСЕ ПОЛЯ ДОЛЖНЫ БЫТЬ КОРНЕВЫМИ
            $mainField = (isset ($this->fields['fields'][$field['name']])) ? $this->fields['fields'][$field['name']] : [];

            // Выставляем метку если на задано
            if (!isset($field['label']) && isset($mainField['label'])) {
                $field['label'] = $mainField['label'];
            }

            $fields[] = $field;
            $fields_prep[] = $this->fieldsPrep->initField(array_replace($mainField, $field));
        }

        // Делаем выборку
        $query = $this->post->paginate($this->config['list']['count-items']);
        if ($query->lastPage() < $query->currentPage()) {
            $query = $this->post->paginate($this->config['list']['count-items'], ['*'], 'page', $query->lastPage());
        }
        // Для пагинации
        $this->dataReturn['items']['currentPage'] = $query->currentPage();
        $this->dataReturn['items']['lastPage'] = $query->lastPage();
        $this->dataReturn['items']['total'] = $query->total();
        // урл страницы списка.
        $this->dataReturn['config']['indexUrl'] = $query->path();

        //Список полей для вывода
        $this->dataReturn['fields'] = $fields;
        $this->dataReturn['config']['title'] = $this->config['lang']['list-title'];

        $this->dataReturn['items']['data'] = [];

        // Добавляем поля поиска
        if (isset($this->fields['search'])) {
            foreach ($this->fields['search'] as $field) {
                unset($field['fields']); // удаляем ненужные опции
                $this->dataReturn['search'][] = $field;
            }
        }

        // Подготваливаем все поля
        foreach ($query->items() as $post) {
            $res = []; // Преобразованные данные

            $res['_links'] = $this->indexLinks($post, $urlPostfix);

            foreach ($fields as $key => $field) {

                $name = $field['name'];

                if (isset($field['func'])) {
                    $func = $field['func'];
                    $res[$name] = $this->$func($post, $field, $urlPostfix);
                    continue;
                }
                // Обработчик полей
                $res[$name] =
                    $fields_prep[$key]->list(Helpers::getDataField($post, $name, ''));
            }
            $this->dataReturn['items']['data'][] = $res;
        }

        //Получаем шаблон
        $templite = (isset($this->config['list']['template'])) ? $this->config['list']['template'] : 'Form::list';

        //Хук перед выходом
        $this->resourceCombineAfter('index');

        if (Request::ajax()) return $this->dataReturn;

        return view($templite, ['data' => $this->dataReturn]);
    }

    // Выводим компоненты
    protected function indexComponents()
    {
        $result = [];
        if (isset($this->config['list']['components']) && is_array($this->config['list']['components'])) {
            foreach ($this->config['list']['components'] as $component) {
                $result[$component['slot'] ?? 'after-buttons'][] = $component;
            }
        }
        return $result;
    }

    // Выводим хлебные крошки.
    protected function indexBreadcrumbs($urlPostfix = '')
    {
        return false;
    }

    // Главное меню в списке. urlPostfix добавочная строка к url адресу.
    protected function indexListMenu($urlPostfix = '')
    {

        $menu = [];

        // Если нужно создавать запись
        if (($btn = $this->indexMenuCreateButton($urlPostfix))) $menu[] = $btn;

        // Для ручной сортировки
        if (($btn = $this->listSortableButton($urlPostfix))) $menu[] = $btn;

        return $menu;
    }

    // Кнопка создать
    protected function indexMenuCreateButton($urlPostfix)
    {
        if ($this->config['list']['create'] && $this->getUserAccess('create')) {
            return [
                'label' => isset($this->config['lang']['create-title'])
                    ? $this->config['lang']['create-title'] : 'Создать',
                'url' => action($this->config['controller-name'] . '@create') . $urlPostfix,
                'btn-type' => 'primary'
            ];
        } else return false;
    }

    // Получаем пункты меню для строки списка
    protected function indexItemMenu()
    {
        if (isset($this->config['list']['item-menu'])) {
            $res = [];
            foreach ($this->config['list']['item-menu'] as $item) {
                // Если есть опция default, то берем значения из дефолтного меню
                if (isset($item['default'])) {
                    // объединяем массивы
                    $newItem = array_replace($this->config['list']['item-menu-default'][$item['default']], $item);
                    // Удаляем опцию дефаулт что бы не передавать в админку
                    unset($newItem['default']);
                    $res[] = $newItem;
                } else $res[] = $item;
            }
            return $res;
        }
        return $this->config['list']['item-menu-default'];
    }

    // Обрабатываем ссылки в списке

    /**
     * Генерирует ссылки в списке для каждой записи. edit,destroy,clone
     * @param Model $post текущая запись
     * @param string $urlPostfix добавочный урл
     * @return array массив ссылок
     */
    protected function indexLinks($post, $urlPostfix)
    {
        $res = [];

        $userId = $post['user_id'] ?? false;

        if ($this->config['list']['item-edit'] && $this->getUserAccess('edit-owner', $userId)) {
            $res['edit'] = $res['edit-show'] = action($this->config['controller-name'] . '@edit', $post['id']);
        }

        if ($this->config['list']['item-view']) {
            $res['view'] = action($this->config['controller-name'] . '@show', $post['id']);
            // Если не установлено, значит реактирование либо отключено либо нет доступ делаем ссылку view
            if (!isset($res['edit-show'])) $res['edit-show'] = $res['view'];
        }

        if ($this->config['list']['item-destroy'] && $this->getUserAccess('destroy-owner', $userId)) {
            $res['destroy'] = action($this->config['controller-name'] . '@destroy', $post['id']);
        }

        if ($this->config['list']['item-clone'] && $this->getUserAccess('create') && $this->getUserAccess('edit-owner', $userId)) {
            $res['clone'] = action($this->config['controller-name'] . '@create')
                . Helpers::mergeUrlParams($urlPostfix, 'clone', $post['id']);
        }

        return $res;
    }

    // Функция для сортировки списка
    protected function indexOrder()
    {
        $order = Request::input('order', false);

        // Если выставлена опция ручной сортировки, то сортировка по умолчанию будет по sort_num
        if (isset($this->config['list']['sortable']) && $this->config['list']['sortable']) {
            $orderField = 'sort_num';
            $orderType = 'asc'; //от меньшего к большему
        } else {
            $orderField = $this->config['list']['default-order']['col'];
            $orderType = $this->config['list']['default-order']['type'];
        }

        if ($order !== false && isset($this->fields['list'][$order]['sortable'])) {
            $orderType = Request::input('order-type', 'desc');
            $orderField = $this->fields['list'][$order]['name'];
            $this->fields['list'][$order]['sortable'] = $orderType;
        }
        $this->post = $this->post->orderBy($orderField, $orderType);
    }

    //Функция поиска для списка, возвращает true если есть что искать.
    protected function indexSearch()
    {

        $searchReq = false;

        //Если есть поля для поиска
        if (isset($this->fields['search'])) {
            //Перебираем
            foreach ($this->fields['search'] as $key => &$field) {
                //Проверяем на валидность
                if (!isset($field['name']) || !isset($field['fields']) || !is_array($field['fields'])) continue;


                //Копируем данные поля из основных полей
                if (isset($field['field-from'])) {
                    // Если поле не существует, удаляем текущее поле из поиска
                    if (!isset($this->fields['fields'][$field['field-from']])) {
                        unset($this->fields['search'][$key]);
                        continue;
                    }

                    $field = array_replace_recursive($this->fields['fields'][$field['field-from']], $field);
                    unset($field['field-from']);
                }

                $field['value'] = Request::input($field['name'], '');

                //Добавляем пустой элемент в начало.
                if (isset($field['options-empty']) && isset($field['options']) && is_array($field['options'])) {
                    array_unshift($field['options'], ['value' => '', 'label' => $field['options-empty']]);
                }

                if ($field['value'] == '') continue;

                $req = $field['value'];

                // Проверяем значения и добавляем дополнительные опции из options
                if ($field['type'] == 'select') {

                    $option = Helpers::searchArray($field['options'], 'value', $field['value']);

                    // Если нет значния
                    if (!$option) abort(403, 'indexSearch: select value not found ' . $field['value']);

                    // подменяем элемент нельзя передать в строке запроса. например null
                    if (array_key_exists('change-value', $option)) {
                        $req = $option['change-value'];
                        unset($option['change-value']);
                    }

                    // Получаем нужные опции
                    foreach (['type-comparison', 'exact-match'] as $key) {
                        if (isset($option[$key])) {
                            $field[$key] = $option[$key];
                            unset($option[$key]);
                        }
                    }

                    if (!isset($field['exact-match'])) $field['exact-match'] = true;
                    if (!isset($field['type-comparison'])) $field['type-comparison'] = '=';
                }

                // Тип выборки, по умолчанию like
                $typeComparison = 'like';
                if (isset($field['type-comparison'])) {
                    $typeComparison = $field['type-comparison'];
                    unset($field['type-comparison']);
                }

                // По умолчанию добавляем %% для запроса
                if (isset($field['exact-match'])) unset($field['exact-match']);
                else $req = '%' . $req . '%';

                //Выборка по группе полей, если в каком то поле есть то данные выведутся
                $this->post = $this->post->where(function ($query)
                use (&$field, $req, $typeComparison, &$searchReq) {
                    $first = true;

                    foreach ($field['fields'] as $column) {
                        // Выборка для релатед полей
                        $func = ($first) ? 'where' : 'orWhere';

                        $searchReq[$column] = $req;

                        if (isset($field['field-save']) && $field['field-save'] == 'relation') {
                            $func .= 'Has';
                            $query = $query->$func('relationFields', function ($query)
                            use ($column, $req, $typeComparison, $first) {
                                $query->where('value', $typeComparison, $req)->where('field_name', $column);
                            });
                        } else $query = $query->$func($column, $typeComparison, $req);

                        $first = false;
                    }
                });
            }
        }

        return $searchReq;
    }
}
