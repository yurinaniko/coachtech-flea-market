<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:20',
            'postal_code' => 'required|regex:/^\d{3}-\d{4}$/',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png|max:10240',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'ユーザー名',
            'postal_code' => '郵便番号',
            'address' => '住所',
            'building' => '建物名',
            'image' => 'プロフィール画像',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ユーザー名を入力してください。',
            'name.max'      => 'ユーザー名は20文字以内で入力してください。',
            'postal_code.required' => '郵便番号を入力してください。',
            'postal_code.regex'    => '郵便番号は「000-0000」のように、ハイフンありの8文字で入力してください。',
            'address.required' => '住所を入力してください。',
            'address.max'      => '住所は255文字以内で入力してください。',
            'building.max' => '建物名は255文字以内で入力してください。',
            'image.image' => 'プロフィール画像は画像ファイルを選択してください。',
            'image.mimes' => 'プロフィール画像は jpeg または png を選択してください。',
            'image.max'   => 'プロフィール画像のサイズは10MB以下にしてください。',
        ];
    }
}

