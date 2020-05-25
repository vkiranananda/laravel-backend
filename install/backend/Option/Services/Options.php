<?php

namespace Backend\Option\Services;

use Backend\Option\Models\Option;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Options
{
    private $options = false;

    /**
     *
     * Возвращает указанну запись, если указан ключ, то вернется значение этого поля.
     *
     * @param string $name название опции :: значение ключа, массива если есть
     * вложенность задается через точку config::site.name
     * @param string|false $key если ключ установлен и опция является элементом массива, то вернет ее значение
     * @param string $default если ключа не найдено, вернет это знчние
     * @return mixed
     */
    public function get($name, $default = false)
    {
        $name = $this->parseName($name);
        // Если опции нет.
        if (!isset($this->options[$name['name']])) {
            $option = Option::where('name', $name['name']);

            // Получаем все опции у которых стоит автозагрузка
            if (!$this->options) {
                $option = $option->orWhere('autoload', true);
                $this->options = [];
            }

            foreach ($option->get() as $opt) {
                $this->options[$opt['name']] = $opt;
            }
        }

        // Получаем значения по ключу если есть.
        if ($name['key']) return Arr::get($this->options[$name['name']]['array_data']['fields'], $name['key'], $default);

        // Если массив не существует возвращаем значение по умолчанию
        if (!isset($this->options[$name['name']]['array_data']['fields'])) return $default;

        // Иначе возвращаем весь массив
        return $this->options[$name['name']]['array_data']['fields'];
    }

    /**
     *
     * Выставляем значение опциям.
     * $replace - Если установлено true то все данные записи будут заменены значением $array,
     * иначе значения $array заменят только те ключи которые есть рекурсивно.
     *
     * @param string $name - название опции :: значение ключа, массива если есть
     * @param mixed $value - значение
     * @param bool $replace - Заменить массив данных целиком или только существующие ключи
     * @param bool $autoload - Если запись новая то добавиться этот параметр автозагрузки
     * @param bool $hidden - Если запись новая то добавиться параметр видимости в админке
     */
    public function set(string $name, $value, $replace = false, $autoload = true, $hidden = true)
    {
        $name = $this->parseName($name);

        // Получаем данные
        $data = $this->get($name['name'], []);

        // Дополнительная проверка на всякий пожарный :)
        if (!isset($this->options[$name['name']])) {
            // Создаем новую запись
            $this->options[$name['name']] = new Option();
            $option = &$this->options[$name['name']];
            $option['autoload'] = $autoload;
            $option['hidden'] = $hidden;
            $option['name'] = $name['name'];
        } else {
            $option = &$this->options[$name['name']];
        }

        // Присваиваем новое значение
        // Если ключ есть
        if ($name['key']) {
            if ($replace) {
                Arr::set($data, $name['key'], $value);
                $newData = $data;
            }
            else {
                // Если рекурсивная замена, получаем текущий массив по ключу
                $repArray = Arr::get($data, $name['key'], []);
                // Если он является массивом и заменяемое значние тоже то делаем замену
                if (is_array($repArray) && is_array($value)) {
                    Arr::set($data, $name['key'], array_replace_recursive($repArray, $value));
                } else {
                    // Если не массив просто заменяем элемент
                    Arr::set($data, $name['key'], $value);
                }
                $newData = $data;
            }
        } else {
            //Если ключа нет меняем корневое значние
            if ($replace) $newData = $value;
            else {
                if (!is_array($data) || !is_array($value)) $newData = $value;
                else $newData = array_replace_recursive($data, $value);
            }
        }

        $option['array_data'] = ['fields' => $newData];
        $option->save();
    }

    /**
     * Удаляем опцию массива, если не указана конкнетная опция в пути, удаляем целиком опцию
     * @param string $name - config::myvalue
     */
    public function foget(string $name)
    {
        $name = $this->parseName($name);

        // Получаем данные
        $data = $this->get($name['name'], []);
        if (!isset($this->options[$name['name']])) return;

        $option = &$this->options[$name['name']];

        // Если ключ не установлен удаляем опцию целиком
        if (!$name['key']) {
            Option::where('id', $option['id'])->delete();
            unset($this->options[$name['name']]);
        } else {
            Arr::forget($data, $name['key']);
            $option['array_data'] = ['fields' => $data];
            $option->save();
        }
    }

    /**
     * Парсим имя на предмет name::key
     * @param string $name
     * @return array массив name, key
     */
    private function parseName($name)
    {
        $parseName = explode("::", $name, 2);

        return [
            'key' => (count($parseName) == 2) ? $parseName[1] : false,
            'name' => $parseName[0]
        ];
    }
}
