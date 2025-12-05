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
            'condition' => 'required|string',
            'description' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'categories' => 'required|array', // 中間テーブル用
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '商品名',
            'brand' => 'ブランド名',
            'condition' => '商品の状態',
            'description' => '商品説明',
            'price' => '価格',
            'image' => '商品画像',
            'categories' => 'カテゴリー',
        ];
    }
}