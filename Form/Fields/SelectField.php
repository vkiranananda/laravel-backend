<?php

namespace Backend\Root\Form\Fields;

use Helpers;

class SelectField extends Field {
	private $options = [];

	function __construct($field)
	{
		//Подготавливаем опции в нужный формат, что бы подставлять верное значение
		if ( isset( $field['options'] ) && is_array($field['options']) ) {
        	$this->options = Helpers::optionsToArr($field['options']);
       	}
		
		parent::__construct($field);
	}

	// Получаем значение для сохраниения
	public function save($value)
	{
		if ($value != '' && ! Helpers::optionsSearch( $this->field['options'], $value ) ) { 
            abort(403, 'SelectField has error in '.$this->field['type'].':'.$this->field['name'].':'.$this->field['value']);
            }
		return $value;
	}

	// Получаем готовое значение для списков
	public function list($value)
	{
		// Мультиселект
		if (is_array($value)) {
			$res = '';
			foreach ($value as $end_value) {
				$end_value = (isset($this->options[$end_value])) ? $this->options[$end_value] : $end_value;
				if ($res != '') $res .= ', ';
				$res .= $end_value;
			}
			return $res;
		} 
		return (isset($this->options[$value])) ? $this->options[$value] : $value;
	}
}


