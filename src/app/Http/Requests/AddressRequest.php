<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    <?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'postal_code' => [
                'required',
                'string',
                'regex:/^[0-9]{3}-[0-9]{4}$/',
            ],
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'postal_code' => '郵便番号',
            'address' => '住所',
            'building' => '建物名',
        ];
    }
}
}
