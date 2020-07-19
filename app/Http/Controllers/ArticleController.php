<?php

namespace App\Http\Controllers;

use App\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        // allメソッドはコレクションを返す
        // sortByDescはコレクションのメソッド
        // 参考: https://readouble.com/laravel/6.x/ja/collections.html
        $articles = Article::all()->sortByDesc('created_at');

        return view('articles.index', ['articles' => $articles]);
    }
}
