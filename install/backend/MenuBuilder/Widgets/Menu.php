<?php

namespace Backend\MenuBuilder\Widgets;

use Backend\MenuBuilder\Models\MenuBuilder;
use Request;

class Menu
{
	public function show($args = [])
	{
		if (!isset ($args['name'])) return 'MenuBulder: параметр name не установлен';
		
		$menu = MenuBuilder::where('name', $args['name'])->first();

		if (!$menu) return 'MenuBulder: меню с имененем "' . $args['name'] . '" не найдено';

		$tree = (is_array($menu['menu'])) ? $menu['menu'] : [];

		$url = "/" . Request::path();
		do {
			$res = $this->setActive($tree, $url);

			// Выходим если нашли совпадение
			if ($res) break;

			$url = preg_replace("/(.*)\/.*/", '${1}', $url);
			
		} while($url != '');

		$template = (isset($args['template'])) ? $args['template'] : 'MenuBuilder::menu';

		return view($template, [ 'menu' => $tree, 'template' => $template ]);
	}

	// Проходимся и выставляем класс active если урл подпадает под раздел
	private function setActive(&$tree, $url)
	{
		$res = false;
		foreach ($tree as &$el) {
			// Ищем сначала во вложенных эементах.
			if (count($el['elements']) > 0) {
				$res = $this->setActive($el['elements'], $url);
				if ($res) $el['active-child'] = true;
			}

			// Потом смотрим текущий, если есть дальше не идем
			if ($el['url'] == $url) {
				$el['active'] = true;
				return true;
			}

			//Если во вложенных был то дальше не идем
			if ($res) return true;

		}
		return $res;		
	}
}