<?php

namespace App\Http\Controllers;

use App\Article;
use App\Http\Requests\ArticleRequest;
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

    // 記事投稿画面
    public function create()
    {
        return view('articles.create');
    }

    // 記事の保存
    // 引数にクラスの型宣言を行うことで、自動的にそのクラスのインスタンスが生成され、
    // メソッド内で使えるようになる
    // これにより、メソッドの内部で他のクラスのインスタンスを生成する必要がなくなり、
    // 他クラスへの依存度合いを減らし、変更がしやすくなる、テストがしやすくなるなどのメリットを享受できるようになる
    // これをDI（依存性の注入）という
    public function store(ArticleRequest $request, Article $article)
    {
        // POSTされたパラメータの配列から、Articleモデルのfillableプロパティ内に指定しておいたプロパティのみを$articleの各プロパティに代入する
        // メリットは不正なリクエストへの対策になること、プロパティの代入の記述を羅列せずに済むこと
        $article->fill($request->all());
        $article->user_id = $request->user()->id;
        $article->save();
        return redirect()->route('articles.index');
    }
}
