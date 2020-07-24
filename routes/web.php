<?php

Auth::routes();
Route::get('/', 'ArticleController@index')->name('articles.index');
Route::resource('/articles', 'ArticleController')->except(['index', 'show'])->middleware('auth');
// 記事詳細画面はログインユーザーじゃなくても見れるようにしたいので、部分的にauthミドルウェアがついていないルートを定義する
Route::resource('/articles', 'ArticleController')->only(['show']);
