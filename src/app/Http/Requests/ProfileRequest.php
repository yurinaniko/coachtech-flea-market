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
                'postal_code' => 'required|regex:/^\d{3}-\d{4}$/',
                'address' => 'required|string|max:255',
                'building' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png|max:10240',
            ];
    }

    public function attributes(): array
    {
        return [
            'postal_code' => '郵便番号',
            'address' => '住所',
            'building' => '建物名',
            'image' => 'プロフィール画像',
        ];
    }

    public function messages(): array
    {
        return [
            'postal_code.regex' => '郵便番号は「000-0000」の形式で入力してください。',
        ];
    }
}
