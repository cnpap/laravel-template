<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password' => 'required|string|between:8,16'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
