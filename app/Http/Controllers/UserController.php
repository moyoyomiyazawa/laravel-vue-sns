<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // ユーザーページ（ユーザーが投稿した記事一覧画面）
    public function show(string $name)
    {
        $user = User::where('name', $name)->first();

        $articles = $user->articles->sortByDesc('created_at');

        return view('users.show', [
            'user' => $user,
            'articles' => $articles,
        ]);
    }

    // いいねした記事一覧画面
    public function likes(string $name)
    {
        $user = User::where('name', $name)->first();

        $articles = $user->likes->sortByDesc('created_at');

        return view('users.likes', [
            'user' => $user,
            'articles' => $articles,
        ]);
    }

    // フォローする
    public function follow(Request $request, string $name)
    {
        $user = User::where('name', $name)->first();

        if ($user->id === $request->user()->id) {
            return abort('404', 'Cannot follow yourself');
        }

        // 1人のユーザーがあるユーザーを複数回重ねてフォローできないようにするため、必ず削除(detach)してから新規登録(attach)する
        $request->user()->followings()->detach($user);
        $request->user()->followings()->attach($user);

        // Laravelではアクションメソッドで連想配列を返すとJSON形式に変換されてレスポンスされる
        return ['name' => $name];
    }

    // フォロー解除
    public function unfollow(Request $request, string $name)
    {
        $user = User::where('name', $name)->first();

        if ($user->id === $request->user()->id) {
            return abort('404', 'Cannot follow yourself');
        }

        $request->user()->followings()->detach($user);

        return ['name' => $name];
    }
}
