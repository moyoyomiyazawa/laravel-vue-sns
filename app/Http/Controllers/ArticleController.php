<?php

namespace App\Http\Controllers;

use App\Article;
use App\Tag;
use App\Http\Requests\ArticleRequest;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct()
    {
        // ArticlePolicyの適用
        $this->authorizeResource(Article::class, 'article');
    }

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
        // タグの自動補完のためにすべてのタグをViewに渡す
        $allTagNames = Tag::all()->map(function ($tag) {
            return ['text' => $tag->name];
        });
        return view('articles.create', [
            'allTagNames' => $allTagNames,
        ]);
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

        // $request->tagsはArticleRequestのpassedValidationメソッドでコレクションになっている
        // eachはコレクションのメソッド
        $request->tags->each(function ($tagName) use ($article) {
            // firstOrCreateメソッドは、引数として渡した「カラム名と値のペア」を持つレコードがテーブルに存在するかどうかを探し、もし存在すればそのモデルを返します。
            // テーブルに存在しなければ、そのレコードをテーブルに保存した上で、モデルを返します。
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            // 記事とタグの紐付け（article_tagテーブルへのレコードの保存）
            $article->tags()->attach($tag);
        });
        return redirect()->route('articles.index');
    }

    // 記事更新画面
    // 引数にArticleクラスの型宣言をすることで、ArticleモデルのインスタンスのDIを行っている
    // DIが行われることで、editアクションメソッド内の$articleにはArticleモデルのインスタンスが代入された状態となる
    // さらに、$articleには、このeditアクションメソッドが呼び出された時のURIが例えばarticles/3/editであれば、idが3であるArticleモデルのインスタンスが代入されます
    // @see 暗黙の結合 https://readouble.com/laravel/6.x/ja/routing.html#implicit-binding
    public function edit(Article $article)
    {
        $tagNames = $article->tags->map(function ($tag) {
            return ['text' => $tag->name];
        });
        // タグの自動補完のためにすべてのタグをViewに渡す
        $allTagNames = Tag::all()->map(function ($tag) {
            return ['text' => $tag->name];
        });
        return view('articles.edit', [
            'article' => $article,
            'tagNames' => $tagNames,
            'allTagNames' => $allTagNames,
        ]);
    }

    // 記事更新処理
    public function update(ArticleRequest $request, Article $article)
    {
        $article->fill($request->all())->save();

        // 一旦記事とタグの既存の紐付けを全削除する
        $article->tags()->detach();
        // タグを登録し直す
        $request->tags->each(function ($tagName) use ($article) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $article->tags()->attach($tag);
        });

        return redirect()->route('articles.index');
    }

    // 記事削除処理
    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index');
    }

    // 記事詳細画面
    public function show(Article $article)
    {
        return view('articles.show', ['article' => $article]);
    }

    // いいねする
    public function like(Request $request, Article $article)
    {
        // 複数回いいねができないようにdetachしてからattachする
        $article->likes()->detach($request->user()->id);
        $article->likes()->attach($request->user()->id);

        // Laravelでは、コントローラーのアクションメソッドで配列や連想配列を返すと、JSON形式に変換されてレスポンスされる
        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }

    // いいねを解除する
    public function unlike(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);

        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }
}
