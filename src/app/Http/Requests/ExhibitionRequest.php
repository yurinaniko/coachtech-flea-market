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
            'name' => 'required|string|max:100',
            'brand' => 'nullable|string|max:255',
            'condition_id' => 'required|integer',
            'description' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'img_url' => 'required|image|mimes:jpeg,png|max:10240',
            'categories' => 'required|array',
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

    public function messages(): array
    {
        return [
            'name.required' => '商品名を入力してください。',
            'name.max' => '商品名は100文字以内で入力してください。',
            'brand.max' => 'ブランド名は255文字以内で入力してください。',
            'condition_id.required' => '商品の状態を選択してください。',
            'description.required' => '商品説明を入力してください。',
            'description.max' => '商品説明は255文字以内で入力してください。',
            'price.required' => '価格を入力してください。',
            'price.integer' => '価格は半角数字で入力してください。',
            'price.min' => '価格は0円以上で入力してください。',
            'img_url.required' => '商品画像を選択してください。',
            'img_url.image' => '商品画像は画像ファイルを選択してください。',
            'img_url.mimes' => '商品画像は jpeg または png を選択してください。',
            'img_url.max' => '商品画像のサイズは10MB以下にしてください。',
            'categories.required' => 'カテゴリーを選択してください。',
        ];
    }
}