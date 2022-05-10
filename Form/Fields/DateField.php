<?php

namespace Backend\Root\Form\Fields;

use Carbon\Carbon;
use GetConfig;
use Log;

class DateField extends Field
{
    // Получаем значение для сохраниения
    public function save($value)
    {
        if ($value == '') return null;

        $dateConfig = $this->getTimeConfig();
        // Применяем локальный часовой пояс
        $date = new Carbon($value, config('app.timezone'));

        return $date;
    }

    // Получаем сырое значние элемента для редактирования
    public function edit($date)
    {
        if ($date == null) return '';

        $dateConfig = $this->getTimeConfig();

        if (!is_object($date)) {
            $date = Carbon::create($date);
            $date->setTimezone(config('app.timezone'));
        }

        // Применяем часовой пояс если дата полная
        if (isset($this->field['time']) && $this->field['time']) {
            return $date->toDateTimeString();
        }

        return $date->toDateString();
    }

    // Получаем готовое значение для списков
    public function list($value)
    {
        if ($value == null) return null;

        $dateConfig = $this->getTimeConfig();

        if (!is_object($value)) $value = Carbon::create($value);

        if (isset($this->field['format'])) {
            $format = $this->field['format'];
        } elseif (isset($this->field['time']) && $this->field['time']) {
            $format = $dateConfig['datetime-format'];
        } else {
            $format = $dateConfig['date-format'];
        }

        return $value->format($format);
    }

    // Получаем настройки таймзоны из админки
    private function getTimeConfig()
    {
        static $dateConfig = false;

        if (!$dateConfig) $dateConfig = GetConfig::backend('backend');

        return $dateConfig;
    }


}
