<?php

namespace Backend\Root\Form\Fields;

use Carbon\Carbon;
use GetConfig;

class DateField extends Field
{
    // Получаем значение для сохраниения
    public function save($value)
    {
        // Надо понять применять ли тайм зоны, тут по идее надо в системную конвернтуть...
        return ($value == '') ? null : $value;
    }

    // Получаем сырое значние элемента для редактирования
    public function edit($value)
    {
        if ($value == null) return null;
        
        $dateConfig = $this->getTimeConfig();

        if (!is_object($value)) $value = Carbon::create($value);

        $value->setTimezone($dateConfig['time-zone']);
        return (isset($this->field['time'])) ? $value->toDateTimeString() : $value->toDateString();
    }

    // Получаем готовое значение для списков
    public function list($value)
    {
        $dateConfig = $this->getTimeConfig();

        if (!is_object($value)) $value = Carbon::create($value);
        $value->setTimezone($dateConfig['time-zone']);

        return (isset($this->field['time'])) ?
            $value->format($dateConfig['datetime-format']) :
            $value->format($dateConfig['date-format']);
    }

    // Получаем настройки таймзоны из админки
    private function getTimeConfig()
    {
        static $dateConfig = false;

        if (!$dateConfig) $dateConfig = GetConfig::backend('backend');

        return $dateConfig;
    }
}
