<?php

namespace Backend\Root\Option\Services;

use Backend\Root\Option\Models\Option;

class Options
{
	private $options = false;

	public function get($name, $key = 'value')
	{
		if(is_array($this->options) && isset($this->options[$name])) {
			return (isset($this->options[$name]['fields'][$key])) ? $this->options[$name]['fields'][$key] : '' ;
		}

		$option = Option::where('name', $name);

		if(!$this->options) {
			$option = $option->orWhere('autoload', '1');
			$this->options = [];
		}

		foreach ($option->get() as $opt) {
			$this->options[$opt['name']] = $opt->array_data;
		}

		if(isset($this->options[$name]) && isset($this->options[$name]['fields'][$key])){
			return $this->options[$name]['fields'][$key];
		}
		return '';
	}


}