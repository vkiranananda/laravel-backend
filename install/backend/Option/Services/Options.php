<?php

namespace Backend\Option\Services;

use Backend\Option\Models\Option;
use Helpers;

class Options
{
	private $options = false;

	public function get($name, $key = false, $default = '')
	{
		$res = $this->_getOption($name, $key, $default);

		if ($res !== false) return $res;

		$option = Option::where('name', $name);

		// Получаем все опции у которых стоит автозагрузка
		if (!$this->options) {
			$option = $option->orWhere('autoload', true);
			$this->options = [];
		}
		foreach ($option->get() as $opt) {
			$this->options[$opt['name']] = [
				'type' => $opt->type,
				'data' => Helpers::getDataField($opt,
					Helpers::getDataField($opt, 'type', ''), '')
			];
		}

		$res = $this->_getOption($name, $key, $default);

		return ($res === false) ? $default : $res;
	}

	// Получаем опцию
	private function _getOption ($name, $key, $default) {

		// Если опции нет.
		if ( !is_array($this->options) || !isset($this->options[$name]) ) return false;

		// Получаем значения по ключу если есть.
		if ( $key ) return Helpers::getDataField($this->options[$name]['data'], $key, $default);

		return $this->options[$name]['data'];
	}

}
