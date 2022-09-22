<?php

namespace Backend\Root\Form\Services;

use GetConfig;
use Helpers;
use Request;
use Validator;
use Log;

// Подготовка и сохарнение полей


class Fields
{

    // Базовые поля идущие в комплекте
    private $fieldsClasses = [
        'default' => '\Backend\Root\Form\Fields\Field',
        'date' => '\Backend\Root\Form\Fields\DateField',
        'files' => '\Backend\Root\Form\Fields\FilesField',
        'gallery' => '\Backend\Root\Form\Fields\FilesField',
        'mce' => '\Backend\Root\Form\Fields\EditorField',
        'editor' => '\Backend\Root\Form\Fields\EditorField',
        'select' => '\Backend\Root\Form\Fields\SelectField',
        'checkbox' => '\Backend\Root\Form\Fields\SelectField',
        'radio' => '\Backend\Root\Form\Fields\SelectField',
        'multiselect' => '\Backend\Root\Form\Fields\SelectField',
        'select-search' => '\Backend\Root\Form\Fields\SelectSearchField',
    ];

    public $request = [];

    function __construct()
    {
        $this->request = Request::all();
    }

    /**
     * Получить все параметры запроса
     * @return array
     */
    public function getRequestAll()
    {
        return $this->request;
    }

    // Инитим поле
    public function initField($field)
    {
        $type = $field['type'] ?? 'default';

        // Подключаем классы обработки полей
        $type_field = (isset($this->fieldsClasses[$type])) ? $type : 'default';

        // Инитим класс обработчик
        return new $this->fieldsClasses[$type_field]($field);
    }

    // Подготавливаем все поля для отображения. Только в readonly
    public function readFields($post, $fields)
    {

        // Выставляем все поля в readonly
        foreach ($fields as &$field) {
            $field['readonly'] = true;
        }

        return $this->editFields($post, $fields);
    }


    // Подготавливаем все поля для отображения. fields корневой список
    public function editFields($post, $fields)
    {
        $arrayData = $post['array_data']['fields'] ?? [];

        // Перебираем корневые поля и устанавлтваем значение по умолчанию
        foreach ($fields as &$field) {

            // Если нет данных полей пропускаем
            if (!isset($field['name']) || !isset($field['type'])) continue;

            //Обрабатываем конкретное поле устанавливая нужные значния
            $field = $this->prepEditField($field, $post, $arrayData);
        }
        // dd($fields['blocks']['fields']['blocks']);
        return $fields;
    }

    // Подготавливаем скрытые поля для отображения. fields только скрытые поля
    public function editHiddenFields($post, $fields)
    {
        // Если не массив или он пуст, возвращаем пустой массив
        if (!is_array($fields) || count($fields) == 0) return [];

        $res = [];

        foreach ($fields as $field) {
            $value = (isset($field['value'])) ? $field['value'] : '';
            $res[$field['name']] = Helpers::getDataField($post, $field['name'], $value);
        }

        return $res;
    }

    // Получаем значение для поля, none - если значение
    private function getFieldValue($field, &$post, $arrayData, $none = false)
    {
        if ($none) return '';

        // Проверяем откуда брать значние. Если условие выполняеся берем из array_data
        if (isset($field['field-save'])
            && ($field['field-save'] == 'array' || $field['field-save'] == 'relation')) {
            if (isset($arrayData[$field['name']])) return $arrayData[$field['name']];
        } //Проверяем есть ли значение в корне записи если field-save не установлен
        elseif (isset($post[$field['name']])) return $post[$field['name']];

        // Выводим значение по умолчанию, если нет пустое значение.
        return (isset($field['value'])) ? $field['value'] : '';
    }

    // Подготавливаем поля для вывода
    private function prepEditField($field, &$post, $arrayData, $none = false)
    {
        // group fields
        if ($field['type'] == 'group') {
            // Подгружаем поля
            if (isset($field['load-from']))
                $field['fields'] = GetConfig::backend($field['load-from']);

            unset($field['load-from']);


            // Присваиваем значение value. Бере
            if (isset($field['field-save'])) {
                $value = (isset($arrayData[$field['name']])) ? $arrayData[$field['name']] : [];
            } else $value = $arrayData;



            // Если readonly то присваиваем это всем влоежнным полям
            if (isset($field['readonly']) && $field['readonly']) $this->setFieldsAttr($field['fields'], 'readonly', true);

            foreach ($field['fields'] as $groupFieldName => &$groupField) {

                // Если поле не имеет name и type пропускаем
                if (!isset($groupField['name']) || !isset($groupField['type'])) continue;

                // Выставляем, что дальнейшие поля будут браться из массива если в корневом указана эта опция или
                if (
                    (isset($field['field-save']) &&
                        ($field['field-save'] == 'array' || $field['field-save'] == 'relation')) ||
                    $groupField['type'] == 'group'
                ) {
                    $groupField['field-save'] = 'array';
                }

               // if ($field['name'] == 'conf-multi-col') dd($field, $groupField, $value,  $this->getFieldValue($groupField, $post, $value, $none), $none);

                // Выставляем значние, берем из value текущего поля
                $groupField['value'] = $this->getFieldValue($groupField, $post, $value, $none);


                //Делаем дополнительные обработки по полям.
                $groupField = $this->prepEditField($groupField, $post, $value, $none);
            }
            unset($field['value']);
        } // для повторителей.
        elseif ($field['type'] == 'repeated') {

            $field['field-save'] = 'array';
            $value = $this->getFieldValue($field, $post, $arrayData, $none);

           // if($field['name'] == 'blocks') dd($field);

            // dd($value);
            // Если значение не установлено, создаем первую запись
            if (!is_array($value)) $value = [];

            // Уникальный индекc
            $field['unique-index'] = 0;

            // Обнуляем value для новых значений.
            $field['value'] = [];

            // Если readonly то присваиваем это всем влоежнным полям
            if (isset($field['readonly']) && $field['readonly']) $this->setFieldsAttr($field['fields'], 'readonly', true);

            // Перебираем массив с value-s
            foreach ($value as $valuesBlock) {

                // Копируем базовые поля
                $field['value'][$field['unique-index']]['fields'] = $field['fields'];
                $field['value'][$field['unique-index']]['key'] = $field['unique-index'];

                // Перебираем поля которые есть и задаем им value
                foreach ($field['value'][$field['unique-index']]['fields'] as &$oneRepField) {

                    // Если поле не имеет name и type пропускаем
                    if (!isset($oneRepField['name']) || !isset($oneRepField['type'])) continue;

                    // if ($field['name'] == 'conf-multi-col')
                    // Полюбому значения в массиве
                    $oneRepField['field-save'] = 'array';

                    // Обрабатываем разные типы и выставляем окончательное значение.
                    $oneRepField = $this->prepEditField($oneRepField, $post, $valuesBlock, $none);
                    // Log::debug($valuesBlock);
                }
                $field['unique-index']++;
            }

            // Устанавливаем умолчания для базовых полей ... Выставляем значение перменной $none
            // Что бы лишний раз не искался value.
            foreach ($field['fields'] as &$baseField) {

                // Если поле не имеет name и type пропускаем
                if (!isset($baseField['name']) || !isset($baseField['type'])) continue;

                $baseField['field-save'] = 'array';

                if (!isset($baseField['value'])) $baseField['value'] = '';

                // Сделал правку тут в конце было true
                $baseField = $this->prepEditField($baseField, $post, []);
            }
        }
        else {
            $field['value'] = $this->initField($field)->edit($this->getFieldValue($field, $post, $arrayData, $none));
        }
        // Убираем лишние опции
        unset($field['field-save']);

        return $field;
    }

    /**
     * Присваиваем атрибут всем полям
     * @param array $fields список полей
     * @param string $attr атрибут
     * @param string $value значение
     */
    private function setFieldsAttr(array &$fields, string $attr, string $value)
    {
        foreach ($fields as &$f) {
            $f[$attr] = $value;
        }
    }


    ///////---------------------------------------Saving-----------------------------------------------

    // Сохраяняем данные. Возвращаем изменненый объект post, fields - полный массив со всеми полями и табами
    public function saveFields($post, $fields)
    {
        $request = $this->request['fields'] ?? [];
        $newFields = [];
        $relationData = [];
        $arrayData = $post['array_data'] ?? [];

        $arrayDataFields = $arrayData['fields'] ?? [];

        // Получаем все поля в табах которые не скрыты
        foreach ($fields['edit'] as $tab) {
            // не скрытый
            if (!isset($tab ['show']) || $this->showCheck($tab['show'], $request) !== false) {

                foreach ($tab['fields'] as $fieldName) { //Массив из доступных полей
                    //Если поля нет в общем списке, например его удалили, то пропускаем обработку
                    if (!isset ($fields['fields'][$fieldName])) continue;

                    $newFields[$fieldName] = $fields['fields'][$fieldName];
                }
            }
        }

        $errors = $this->saveFieldsList($newFields, $this->request['fields'] ?? [], $post, $arrayDataFields, $relationData);

        // Сохраняем скрытые поля
        if (isset ($fields['hidden']) && is_array($fields['hidden'])) {
            // Проставляем всем полям тип
            $hiddenFields = [];
            foreach ($fields['hidden'] as $field) {
                $field['type'] = 'hidden';
                $hiddenFields[] = $field;
            }
            $this->saveFieldsList(
                $hiddenFields,
                $this->request['hidden'] ?? [],
                $post,
                $arrayData,
                $relationData
            );
        }

        // Устанавливаем array_data
        if (count($arrayDataFields) > 0) {
            $arrayData['fields'] = $arrayDataFields;
            $post['array_data'] = $arrayData;
        }

        return [
            'errors' => $errors,
            'relations' => $relationData,
            'post' => $post
        ];
    }


    /**
     * Сохраняем одно поле
     * @param $post
     * @param $field
     * @param $value
     * @return array возвращаем модифицированный $post и error если есть, иначе true.
     */
    public function saveField($post, $field, $value)
    {
        $fields[$field['name']] = $field;
        $relationData = [];
        $arrayDataFields = [];

        $errors = $this->saveFieldsList($fields, [$field['name'] => $value], $post, $arrayDataFields, $relationData);

        if ($errors !== true) {
            $errors = $errors[$field['name']];
        }

        return [
            'error' => $errors,
            'post' => $post
        ];
    }


    // Обходит массив полей и сохраняет данные. Рекурсивная функция
    private function saveFieldsList(
        $fields, // Список полей
        $request, // Данные формы
        &$post, //  Пост
        &$arrayData, // Массивные данные
        &$relationData, // Данные для сохранения в другую таблицу
        $fieldSave = null // Куда сохраняем поле
    )
    {

        $res = [];
        $errors = [];

        foreach ($fields as $field) {
            // Если не установлены нужные параметры не обрабатываем
            if (!isset($field['name']) || !isset($field['type'])) continue;

            // Если поле только чтение не обрабатываем
            if (isset($field['readonly']) && $field['readonly']) continue;

            // Html поля пропускаем
            if (array_search($field['type'], ['none', 'html', 'html-title'])) continue;

            // Если поле скрыто, так же не обрабатываем
            if (isset($field['show']) && $this->showCheck($field['show'], $request) === false) continue;

            $fieldName = $field['name'];

            // Выставляем value
            $field['value'] = (isset($request[$fieldName])) ? $request[$fieldName] : '';

            // Выставляем fieldSave
            if (isset($fieldSave)) $field['field-save'] = $fieldSave;

            $error = $this->saveFieldData($field, $post, $arrayData, $relationData);

            if ($error !== true) $errors [$field['name']] = $error;
        }

        return (count($errors) > 0) ? $errors : true;
    }

    // Обработка конечного поля
    private function saveFieldData($field, &$post, &$arrayData, &$relationData)
    {

        $value = $field['value'];

        if (!isset($field['field-save'])) $field['field-save'] = null;

        //------------------------------Group------------------------------------

        if ($field['type'] == 'group') {

            // Подгружаем данные если нужно
            if (isset($field['load-from'])) {
                $field['fields'] = GetConfig::backend($field['load-from']);
            }

            $error = $this->saveFieldsList($field['fields'], $value, $post, $arrayData[$field['name']], $relationData, $field['field-save']);

            return $error;
        } //------------------------------Repeated---------------------------------

        elseif ($field['type'] == 'repeated') {

            // Если данных нет выходим
            if (!is_array($value)) return;

            // Обнуляем предыдущие данные.
            $arrayData[$field['name']] = [];

            $indexRepBlock = 0;
            $errors = [];

            // Перебираем блоки репитед полей
            foreach ($value as $repData) {

                // Тут важно что переменная с сервера идет не значением, а объектом, где указан ключ
                // группы репитед, оно надо для отображения ошибок и при сортировке что бы формы не
                // рендерились поновой.
                // Продолжаем обработку полей.

                $arrayData[$field['name']][$indexRepBlock] = [];

                $error = $this->saveFieldsList(
                    $field['fields'],
                    $repData['value'],
                    $post,
                    $arrayData[$field['name']][$indexRepBlock],
                    $relationData, 'array'
                );

                $indexRepBlock++;
                // Обрабатываем ошибки
                if ($error !== true) $errors [$repData['key']] = $error;
            }

            return (count($errors) > 0) ? $errors : true;

        }

        $value = $this->initField($field)->save($value);

        //Правила валидации
        if (isset($field['validate'])) {
            $v = Validator::make([$field['name'] => $value], [$field['name'] => $field['validate']]);

            if ($v->fails()) return implode(' ', $v->errors()->all());
        }


        // Сохраняем данные в массив
        if (($field['field-save'] == 'array' || $field['field-save'] == 'relation')) {
            // Log::debug($value);
            $arrayData[$field['name']] = $value;
            //Добавляем в связи
            if ($field['field-save'] == 'relation') {
                $relationData[$field['name']] = $value;
            }
        } elseif ($field['field-save'] != 'none') {
            $post[$field['name']] = $value;
        }

        return true;
    }


    //Проверка условий на видимость.
    private function showCheck($show, &$data)
    {
        $res = false;

        foreach ($show as $key => $showBlock) {
            //Если поля проверочного нет, выводим отказ
            if (!isset($data[$showBlock['field']])) return false;

            if ($key != 0) { //не первая запись
                //Оператор &&, если предыдущее условие ошибка тогда сл тоже ошибка, проверку не делаем
                if ($showBlock['operator'] == '&&' && $res == false) continue;
                //Опертор ||, если предыдущее истинно, тогда возвращем истину, если ложно делаем проверки дальше.
                if ($showBlock['operator'] == '||' && $res == true) return $res;
            }

            // Проверяем соответсвия условиям
            if ($showBlock['type'] == '==') {
                if ($data[$showBlock['field']] == $showBlock['value']) $res = true;
                else $res = false;
            } else { //!=
                if ($data[$showBlock['field']] != $showBlock['value']) $res = true;
                else $res = false;
            }
        }

        return $res;
    }
}
