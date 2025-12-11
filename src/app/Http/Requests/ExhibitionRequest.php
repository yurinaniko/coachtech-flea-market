<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:20',
            'brand' => 'nullable|string|max:255',
            'condition_id' => 'required|integer',
            'description' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'img_url' => 'required|image|mimes:jpeg,png|max:10240',
            'categories' => 'required|array', // 中間テーブル用
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '商品名',
            'brand' => 'ブランド名',
            'condition_id' => '商品の状態',
            'description' => '商品説明',
            'price' => '価格',
            'img_url' => '商品画像',
            'categories' => 'カテゴリー',
        ];
    }
}