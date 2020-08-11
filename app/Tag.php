<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
    ];

    // アクセサ
    // 呼び出し時は$tag->hashtagといった記述になる
    public function getHashtagAttribute(): string
    {
        return '#' . $this->name;
    }

    // 記事モデルとの多対多のリレーションを定義
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany('App\Article')->withTimestamps();
    }
}
