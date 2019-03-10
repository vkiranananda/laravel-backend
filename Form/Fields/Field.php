<?php

namespace Backend\Root\Form\Fields;

class Field {

	protected $field = [];

	function __construct($field)
	{
		$this->field = $field;
	}

	// Получаем значение для сохраниения
	public function save($value)
	{
		return $value;
	}

	// Получаем сырое значние элемента для редактирования
	public function edit($value)
	{
		return $value;
	}

	// Получаем готовое значение для списков
	public function list($value)
	{
		return $value;
	}
}