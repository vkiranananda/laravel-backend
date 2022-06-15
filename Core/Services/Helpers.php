<?php

namespace Backend\Root\Core\Services;
use \Illuminate\Database\Eloquent\Collection;
class Helpers
{

    // Получаем все поля, где есть массив options, передается параметр табов
    static public function changeFieldsOptions($fields)
    {
        $res = [];

        foreach ($fields as $key => &$field) {
            if (isset($field['options']) && is_array($field['options'])) {
                $field['options'] = Helpers::optionsToArr($field['options']);
            }
        }
        return $fields;
    }

    // Объединяем параметры урл
    static public function mergeUrlParams($url, $param, $value)
    {
        $url .= ($url != '') ? '&' : '?';

        return $url . $param . '=' . $value;
    }

    // Получаем массив списка записей с нужными полями
    static public function getArrayItems(&$data, $fields, $colums = ['id'])
    {
        $res = [];
        foreach ($data as $el) {
            $item = [];
            foreach ($fields as $field) {
                $item[$field['name']] = Helpers::getDataField($el, $field['name']);
                if (isset($field['options'])
                    && is_array($field['options'])
                    && isset($field['options'][$item[$field['name']]])) {
                    $item[$field['name']] = $field['options'][$item[$field['name']]];
                }
            }
            foreach ($colums as $col) {
                $item[$col] = Helpers::getDataField($el, $col);
            }
            $res[] = $item;
        }
        return $res;
    }

    // Заполняем массив значениями keys
    static public function setArray(&$from, $keys)
    {
        $res = [];
        foreach ($keys as $key) {
            $res[$key] = (isset($from[$key])) ? $from[$key] : '';
        }
        return $res;
    }

    // Поиск в массиве по ключу и значению
    static public function searchArray($arr, $key, $val)
    {
        foreach ($arr as $vArr) {
            if (isset($vArr[$key]) && $vArr[$key] == $val) return $vArr;
        }
        return false;
    }

    // Генерит список value label для селектов и прочего из списка данных
    static public function getHtmlOptions($from)
    {
        $res = [];
        if (is_array($from) || $from instanceof Collection) {
            foreach ($from as $el) {
                $r['value'] = $el['id'];
                $r['label'] = $el['name'];
                $res[] = $r;
            }
        }
        return $res;
    }

    // Фукнция генерит из списка опций ассиотивный массив
    static public function optionsToArr($options)
    {
        $res = [];
        foreach ($options as $option) {
            $res[$option['value']] = $option['label'];
        }
        return $res;
    }

    // Ищем по опциям (value) можно передать и простой массив,тогда будет искаться по значению. Первый параметр список опиций, второ искомая строка или массив значний
    static public function optionsSearch($arr, $str)
    {
        if (!is_array($arr)) return false;

        $count = 0;

        foreach ($arr as $el) {
            if (is_array($el)) {
                if (!isset($el['value'])) {
                    $el['value'] = '';
                }
            } else $el = ['value' => $el];

            if (is_array($str)) {
                foreach ($str as $lastStr) {
                    if ($el['value'] == $lastStr) {
                        $count++;
                    }
                }
            } elseif ($el['value'] == $str) return true;
        }

        if (is_array($str) && count($str) == $count) return true;

        return false;
    }

    // Получает список айдишников из списка записей
    static public function getListIds(&$from)
    {
        $res = [];
        if (is_array($from)) {
            foreach ($from as $el) {
                $res[] = $el['id'];
            }
        }
        return $res;
    }

    // Выводим данные поля, если данных нет выводим возвращаем заничение 3 параметр, по умолчанию false
    static public function getDataField($data, $key, $res = false)
    {
        $result = $res;
        foreach (explode(".", $key) as $i => $k) {
            if ($i == 0) {
                // Ищем в корне записи
                if (isset($data[$k])) $result = $data[$k];
                // Ищем в массиве array_data
                elseif (isset($data['array_data']['fields'][$k]))
                    $result = $data['array_data']['fields'][$k];
                // Ничего не найдено
                else return $res;
            } else {
                if (isset($result[$k])) $result = $result[$k];
                else return $res;
            }
        }
        return $result;
    }

    // То же самое что предыдущее но выводим res если значиение пустое.
    static public function getDataFieldEmpty($data, $key, $res = false)
    {
        $value = Helpers::getDataField($data, $key);

        if ($value != '' && $value !== false) return $value;

        return $res;
    }

    // Генерит список атрибутов для хтмл поля из массива key=value
    static public function getAttrs($attr = [])
    {
        $res = '';
        foreach ($attr as $key => $value) {
            $res .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
        return $res;
    }

}
