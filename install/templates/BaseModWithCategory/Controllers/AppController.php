<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Backend\BaseMod\Models\BaseMod;
use Backend\Category\Models\Category;


class BaseModController extends Controller
{
	// Список записей
	// $cats все ID категорий которые подпадают под этот урл, первая категория идет под индексом 0, далее все вложенные
    public function index($cats = [])
    {
    	// 
    	$cat = Category::whereIn('id', $cats)->get();

    	if ( count($cat) < 1 ) abort(404, 'Category not found');

    	$pages = BaseMod::whereIn('category_id', $cats)->paginate(12);

    	dd ($cat, $pages);
    	// return view( 'pages.list', [ 'cat' => $cat, 'pages' => $pages ] );
    }

    // Конечная запись, $cats - массив ID категорий, под какие подпадает данный урл.
    public function show($cats, $id)
    {

    	$page = BaseMod::whereIn('category_id', $cats);

    	// Если используете url
    	// if (is_numeric($id)) $page = $page->where('id', $id)->where('url', '')->first();
    	// else $page = $page->where('url', $id)->first();

    	$page = $page->where('id', $id)->first();

    	if (!$page) abort(404);

    	return view( 'pages.page', [ 'page' => $page ] );
    }
}