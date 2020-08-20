<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // ユーザーページ
    public function show(string $name)
    {
        $user = User::where('name', $name)->first();

        return view('users.show', [
            'user' => $user,
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
