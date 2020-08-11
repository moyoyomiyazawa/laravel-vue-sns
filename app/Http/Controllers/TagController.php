<?php

namespace App\Http\Controllers;

use App\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    // タグ毎の記事一覧画面
    public function show(string $name)
    {
        // $nameと一致するタグ名を持つタグモデルをコレクションで取得し、firstメソッドで要素を取り出す
        $tag = Tag::where('name', $name)->first();
        return view('tags.show', ['tag' => $tag]);
    }
}
