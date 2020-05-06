<?php

namespace Backend\Root\Form\Fields;

use Carbon\Carbon;
use GetConfig;

class DateField extends Field
{
    // Получаем значение для сохраниения
    public function save($value)
    {
        if ($value == '') return null;

        $dateConfig = $this->getTimeConfig();

        // Применяем локальный часовой пояс
        $date = new Carbon($value, $dateConfig['time-zone']);

        // Делаем смещение до системного.
        $date->setTimezone(config('app.timezone'));

        return $date;
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
        if ($value == null) return null;

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
