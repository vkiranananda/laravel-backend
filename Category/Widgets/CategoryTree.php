<?php

namespace Backend\Root\Category\Widgets;
use Content;


class  CategoryTree
{
	
	public static function show($args = '')
	{
		//$sliders = \Backend\Root\Option\Models\Option::where('name', 'Слайдер на главной')->get();
		//if(count($sliders) != 1)return '';

		return view('Category::tree');
	}
}