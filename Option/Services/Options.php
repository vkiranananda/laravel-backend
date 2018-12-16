<?php

namespace Backend\Root\Option\Services;

use Backend\Root\Option\Models\Option;

class Options
{
	private $options = false;

	public function get($name, $key = false)
	{

		$res = $this->_getOption($name, $key);

		if ($res !== false) return $res;

		$option = Option::where('name', $name);

		if (!$this->options) {
			$option = $option->orWhere('autoload', '1');
			$this->options = [];
		}
		foreach ($option->get() as $opt) {
			$data = ( isset ($opt->array_data['fields']) ) ? $opt->array_data['fields'] : '';
			$this->options[$opt['name']] = [ 
				'type' => $opt->type,
				'data' => $data
			];
		}

		return $this->_getOption($name, $key);
	}

	//Получаем опция
	private function _getOption ($name, $key) {

		if ( is_array($this->options) && isset($this->options[$name]) ) {
			
			$res = $this->options[$name]['data'];
			$type = $this->options[$name]['type'];
			
			// Если не установлено или не массив выводим значение по умолчанию
			if ( !is_array($res) ) return '';

			if ( !$key ) {
				//Если установлен тип и поле всего одно то выводим его
				if ($type != '' && count($res) == 1 && isset( $res[ $type ]) ) {
					return $res[ $this->options[$name]['type'] ];
				} else {
					return $res;
				}
			}
			
			//Получаем значения по ключу если есть.
			if ( isset($res[$key]) ) return $res[$key];

			//Выводим пустое значение
			return '';
		} 
		return false;
	}

}