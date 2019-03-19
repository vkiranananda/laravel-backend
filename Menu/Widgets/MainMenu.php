<?php

namespace Backend\Root\Menu\Widgets;
use GetConfig;

class MainMenu
{
	public function show($args = '')
	{
		$menu = GetConfig::backend("main-menu");

		foreach ($menu as &$item) {
			if (!isset($item['type'])) continue;

			if ($item['type'] == 'method') {
				$item['items'] = $item['method']();
			}
		}
		
		return view('Menu::widgets.main-menu', [ 'menu' => $menu ]);
	}
}