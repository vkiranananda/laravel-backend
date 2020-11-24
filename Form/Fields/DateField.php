<?php

namespace Backend\Root\Form\Fields;

use Carbon\Carbon;
use GetConfig;
use Illuminate\Support\Facades\Log;

class DateField extends Field
{
    // Получаем значение для сохраниения
    public function save($value)
    {
        if ($value == '') return null;

        $dateConfig = $this->getTimeConfig();
        // Применяем локальный часовой пояс
        $date = new Carbon($value, $dateConfig['time-zone']);

        // Делаем смещение до системного, только если дата целиком
        if (isset($this->field['time']) && $this->field['time'])
            $date->setTimezone(config('app.timezone'));

        return $date;
    }

    // Получаем сырое значние элемента для редактирования
    public function edit($value)
    {
        if ($value == null) return null;

        $dateConfig = $this->getTimeConfig();

        if (!is_object($value)) $value = Carbon::create($value);

        // Применяем часовой пояс если дата полная
        if (isset($this->field['time']) && $this->field['time']) {
            $value->setTimezone($dateConfig['time-zone']);
            return $value->toDateTimeString();
        }

        return $value->toDateString();
    }

    // Получаем готовое значение для списков
    public function list($value)
    {
        if ($value == null) return null;

        $dateConfig = $this->getTimeConfig();

        if (!is_object($value)) $value = Carbon::create($value);

        // Применяем часовой пояс если дата полная
        if (isset($this->field['time']) && $this->field['time']) {
            $value->setTimezone($dateConfig['time-zone']);
            return $value->format($dateConfig['datetime-format']);
        }

        return $value->format($dateConfig['date-format']);
    }

    // Получаем настройки таймзоны из админки
    private function getTimeConfig()
    {
        static $dateConfig = false;

        if (!$dateConfig) $dateConfig = GetConfig::backend('backend');

        return $dateConfig;
    }


}
