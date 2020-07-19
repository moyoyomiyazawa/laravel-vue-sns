<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    /**
     * Articleモデルから、紐づくユーザーモデルのプロパティにアクセスできるように、リレーションを定義する
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        // usersテーブルの主キーはid、articlesテーブルの外部キーはuser_idであるとLaravel側で判別している
        // 上記ルールに沿っていない場合は、追加引数を渡す必要がある
        return $this->belongsTo('App\User');
    }
}
