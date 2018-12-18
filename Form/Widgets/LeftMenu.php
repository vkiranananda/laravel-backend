<?php

namespace Backend\Root\Form\Widgets;
use GetConfig;

class LeftMenu
{
	
	public static function show($args = '')
	{
		return view('Form::widgets.left-menu', [
			'menu' 		=> GetConfig::backend("left-menu"), 
			'cats' 		=> \Backend\Root\Category\Services\Menu::getCats()
		]);
	}
}