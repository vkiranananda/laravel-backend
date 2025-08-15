<?php

namespace Backend\Root\Menu\Widgets;

use Backend\Root\User\Services\UserAccess;
use GetConfig;

class MainMenu
{
	public function show($args = '')
	{
		$menu = GetConfig::backend('main-menu');
		$resMenu = [];
		foreach ($menu as $item) {
			if (isset($item['user-access-key'])) {
				if (!UserAccess::checkAccess('read-owner', $item['user-access-key'])) {
					continue;
				}
			}
			
			if (!isset($item['type'])) {
				$resMenu[] = $item;
				continue;
			}

			if ($item['type'] == 'method') {
				$item['items'] = $item['method']();
			}
			$resMenu[] = $item;
		}

		return view('Menu::widgets.main-menu', ['menu' => $resMenu]);
	}
}
