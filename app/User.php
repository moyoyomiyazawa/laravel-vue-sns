<?php

namespace App;

use App\Mail\BareMail;
use App\Notifications\PasswordResetNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token, new BareMail()));
    }

    // ユーザーが投稿した記事モデルにアクセスできるようにリレーションを定義
    public function articles(): HasMany
    {
        return $this->hasMany('App\Article');
    }

    // あるユーザーをフォローしているのユーザーの一覧を取得
    public function followers(): BelongsToMany
    {
        // リレーション元: usersテーブル
        // リレーション先: usersテーブル
        // 中間テーブル: followsテーブル
        // リレーション元のusersテーブルのidは中間テーブルfollowsテーブルのfollowee_idと紐付く
        // リレーション先のusersテーブルのidは中間テーブルfollowsテーブルのfollower_idと紐付く
        return $this->belongsToMany('App\User', 'follows', 'followee_id', 'follower_id')->withTimestamps();
    }

    // あるユーザーがフォローしているユーザーの一覧を取得
    public function followings(): BelongsToMany
    {
        return $this->belongsToMany('App\User', 'follows', 'follower_id', 'followee_id')->withTimestamps();
    }

    // ユーザーがいいねした記事のリレーション定義
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany('App\Article', 'likes')->withTimestamps();
    }

    // ログインユーザーが現在表示中のユーザーをフォローしているかどうかを判定する
    public function isFollowedBy(?User $user): bool
    {
        return $user
            ? (bool)$this->followers->where('id', $user->id)->count()
            : false;
    }

    /**
     * フォロワー数を取得
     *
     * @return integer
     */
    public function getCountFollowersAttribute(): int
    {
        return $this->followers->count();
    }

    /**
     * フォロワー中のユーザー数を取得
     *
     * @return integer
     */
    public function getCountFollowingsAttribute(): int
    {
        return $this->followings->count();
    }
}
