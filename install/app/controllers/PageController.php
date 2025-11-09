<?php

namespace App\Http\Controllers;

use Backend\Page\Models\Page;


class PageController extends Controller
{
    public function index($cats = [])
    {
        // Получаем главную страницу
        return $this->show($cats, 'index');
    }

    public function show($cats, $id)
    {
        $urlKey = is_numeric($id) ? 'id' : 'url';
        $page = Page::whereIn('category_id', $cats)->where($urlKey, $id)->first();

        if (!$page) abort(404);

        // Грузим картинки оптом для целиком поста
        // UploadedFiles::loadByPost($page);

        return view('page.page', ['page' => $page]);
    }
}
