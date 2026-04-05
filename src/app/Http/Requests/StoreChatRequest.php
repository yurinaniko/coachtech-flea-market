<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChatRequest extends FormRequest
{
    public function rules()
    {
        return [
            'comment' => 'required|string|max:400',
            'image' => 'nullable|image|mimes:jpeg,png|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'comment.required' => 'コメントを入力してください',
            'comment.max' => 'コメントは400文字以内で入力してください',
            'image.image' => '画像ファイルをアップロードしてください',
            'image.mimes' => '「jpeg」または「png」形式でアップロードしてください',
            'image.max' => '画像は2MB以内にしてください',
        ];
    }
}