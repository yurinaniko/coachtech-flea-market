<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => 'required|in:card,konbini',
            'postal_code' => [
            'required',
            'string',
            'regex:/^[0-9]{3}-[0-9]{4}$/',
            ],
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => '郵便番号はハイフンありの8文字で入力してください',
            'address.required'  => '住所を入力してください',
            'payment_method.required' => '支払い方法を選択してください',
            'payment_method.in' => '支払い方法を選択してください',
        ];
    }
}