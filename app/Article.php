<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    // Articleモデルにおいて、いじっていもいいプロパティは下記のみだよ、という意味
    protected $fillable = [
        'title',
        'body',
    ];

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

    /**
     * 記事モデルとユーザーモデルの、中間テーブルlikeを通じた多対多のリレーションを定義する
     *
     * @return BelongsToMany
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany('App\User', 'likes')->withTimestamps();
    }

    /**
     * ユーザーが記事をいいね済かどうかを判定する
     *
     * @param User|null $user
     * @return boolean
     */
    public function isLikedBy(?User $user): bool
    {
        // ゲストの場合はfalseを返す（Auth::user()は未ログインの場合nullを返す為）
        return $user
            // $this->likesは記事モデルに、中間テーブルlikesテーブル経由で紐付くユーザーを取得して、コレクションで返す（記事をいいねしたユーザーを返す）
            // 今ログインしているユーザーのidと一致しているレコードがあった場合、trueを返す
            // 一致したレコードがなければfalseを返す
            ? (bool)$this->likes->where('id', $user->id)->count()
            : false;
    }

    /**
     * 記事のいいね数を取得する
     *
     * @return integer
     */
    public function getCountLikesAttribute(): int
    {
        return $this->likes->count();
    }
}
