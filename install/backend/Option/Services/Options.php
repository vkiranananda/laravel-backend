<?php

namespace Backend\Option\Services;

use Backend\Option\Models\Option;
use Helpers;

class Options
{
    private $options = false;

    /**
     *
     * Возвращает указанну запись, если указан ключ, то вернется значение этого поля.
     *
     * @param string $name название опции
     * @param string|false $key если ключ установлен и опция является элементом массива, то вернет ее значение
     * @param string $default если ключа не найдено, вернет это знчние
     * @return mixed
     */
    public function get($name, $key = false, $default = '')
    {

        // Если опции нет.
        if (!isset($this->options[$name])) {
            $option = Option::where('name', $name);

            // Получаем все опции у которых стоит автозагрузка
            if (!$this->options) {
                $option = $option->orWhere('autoload', true);
                $this->options = [];
            }

            foreach ($option->get() as $opt) {
                $this->options[$opt['name']] = $opt;
            }
        }

        // Если массив не существует возвращаем значение по умолчанию
        if (!isset($this->options[$name]['array_data']['fields'])) return $default;

        // Получаем значения по ключу если есть.
        if ($key) return Helpers::getDataArray($this->options[$name]['array_data']['fields'], $key, $default);

        // Иначе возвращаем весь массив
        return $this->options[$name]['array_data']['fields'];
    }

    /**
     *
     * Выставляем значение опциям.
     * $replace - Если установлено true то все данные записи будут заменены значением $array,
     * иначе значения $array заменят только те ключи которые есть рекурсивно.
     *
     * @param string $name - Название
     * @param array $array - Массив данных
     * @param bool $replace - Заменить массив данных целиком или только существующие ключи
     * @param bool $autoload - Если запись новая то добавиться этот параметр автозагрузки
     * @param bool $hidden - Если запись новая то добавиться параметр видимости в админке
     */
    public function set(string $name, array $array, $replace = false, $autoload = true, $hidden = true)
    {
        // Получаем данные
        $data = $this->get($name, false, []);

        // Дополнительная проверка на всякий пожарный :)
        if (!isset($this->options[$name])) {
            // Создаем новую запись
            $this->options[$name] =  new Option();
            $option = &$this->options[$name];
            $option['autoload'] = $autoload;
            $option['hidden'] = $hidden;
            $option['name'] = $name;
        } else {
            $option = &$this->options[$name];
        }

        // Присваиваем новое значение
        if ($replace) $data = $array;
        else $data = array_replace_recursive($data, $array);

        $option['array_data'] = ['fields' => $data];

        $option->save();
    }
}
