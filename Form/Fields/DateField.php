<?php

namespace Backend\Root\Form\Fields;

class DateField extends Field {

	// Получаем значение для сохраниения
	public function save($value)
	{
		return ($value == '') ? null : $value;
	}

	// Получаем сырое значние элемента для редактирования
	public function edit($value)
	{
    	if (is_object($value)) {
    		return (isset($this->field['time'])) ? $value->toDateTimeString() : $value->toDateString();
    	} else 
    		return $value;
	}

	// Получаем готовое значение для списков
	public function list($value)
	{
		return $value;
	}
}