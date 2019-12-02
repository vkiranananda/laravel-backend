<?php

namespace Backend\MenuBuilder\Widgets;

use Backend\MenuBuilder\Models\MenuBuilder;

class Menu
{
	public function show($args = [])
	{
		if (!isset ($args['name'])) return 'MenuBulder: параметр name не установлен';
		
		$menu = MenuBuilder::where('name', $args['name'])->first();

		if (!$menu) return 'MenuBulder: меню с имененем "' . $args['name'] . '" не найдено';

		$tree = (is_array($menu['menu'])) ? $menu['menu'] : [];

//		$this->treeToList($tree);

		$template = (isset($args['template'])) ? $args['template'] : 'MenuBuilder::menu';
		
		return view($template, [ 'menu' => $tree, 'template' => $template ]);
	}

	// Если нужна рекурся для чего то можно дописать :)
	private function treeToList(&$tree, $depth = 0)
	{
		foreach ($tree as &$el) {
			$el['depth'] = $depth;
			if (count($el['elements']) > 0) $this->treeToList($el['elements'], $depth + 1);
		}
	}
}