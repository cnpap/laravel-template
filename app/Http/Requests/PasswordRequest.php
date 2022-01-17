<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password' => 'required|string|between:250,450',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
