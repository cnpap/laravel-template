<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids'   => 'required|array|between:1,30',
            'ids.*' => 'int'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
