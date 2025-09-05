<?php

namespace App\Http\Controllers;

use Backend\Category\Models\Category;
use Backend\News\Models\News;
use Carbon\Carbon;

use UploadedFiles;

class NewsController extends Controller
{
    public function index($cats = [])
    {
        $cat = Category::whereIn('id', $cats)->get();

        if (count($cat) < 1)
            abort(404, 'Category not found');

        $news = News::whereIn('category_id', $cats)
            ->where('status', 1)
            ->where('publication_date', '<=', date('Y-m-d'))
            ->orderBy('publication_date', 'desc')
            ->paginate(12);

        foreach ($news as &$item) {
            $date = Carbon::parse($item->publication_date);
            $item['date'] = $date->format('d.m.Y');
        }
        // UploadedFiles::loadByList($pages, 'icon');
        return view('news.list', ['cat' => $cat, 'news' => $news]);
    }

    public function show($cats, $id)
    {
        $urlKey = is_numeric($id) ? 'id' : 'url';
        $news = News::whereIn('category_id', $cats)->where($urlKey, $id)->first();

        if (!$news)
            abort(404);

        // UploadedFiles::loadByPost($news);

        return view('news.page', ['news' => $news, 'cat' => Category::where('id', $news['category_id'])->first()]);
    }
}
