<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:50',
            'body' => 'required|max:500',
            // /^(?!.*\s).+$/uは、PHPにおいて半角スペースが無いこと、/^(?!.*\/).*$/uは/が無いことをチェックする正規表現
            'tags' => 'json|regex:/^(?!.*\s).+$/u|regex:/^(?!.*\/).*$/u',
        ];
    }

    // バリデーションメッセージの:attribute部分をカスタム属性名へ置き換える
    public function attributes()
    {
        return [
            'title' => 'タイトル',
            'body' => '本文',
            'tags' => 'タグ',
        ];
    }

    /**
     * バリデーション成功後に自動的に呼ばれるメソッド
     *
     * @return void
     */
    public function passedValidation()
    {
        // json文字列を連想配列に変換し、更にコレクションに変換
        $this->tags = collect(json_decode($this->tags))
            // タグ登録できるのは5個までなので切り詰める
            ->slice(0, 5)
            // mapメソッドを使ってほしい要素だけ取り出す
            ->map(function ($requestTag) {
                return $requestTag->text;
            });
    }
}
