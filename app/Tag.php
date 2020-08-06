<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
