<?php

namespace Backend\Root\Form\Widgets;
use GetConfig;

class LeftMenu
{
	
	public static function show($args = '')
	{
		$cats = ( isset(GetConfig::backend("backend")['category-disable']) ) ? [] : \Backend\Category\Services\Menu::getCats();

		return view('Form::widgets.left-menu', [
			'menu' 		=> GetConfig::backend("left-menu"), 
			'cats' 		=> $cats
		]);
	}
}