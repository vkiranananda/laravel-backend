<?php

namespace Backend\Root\Category\Services;

use Categories;

class Menu
{
	//Получаем список всех корневых категорий
	static public function getCats()
	{

		$res = [];

		foreach (Categories::getCat() as $cat) {
			$res[] = [
				'label'	=> $cat['name'],
				'icon'	=> $cat['conf']['icon'],
				'url'	=> action(Categories::getModuleControllers($cat['mod'])['resourceController'].'@index').'?cat='.$cat['id']
			];	
		}
		return $res;		
	}
}