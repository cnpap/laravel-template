<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'     => 'string',
            'status'   => 'array|max:4',
            'status.*' => 'string|in:' . STATUS_JOIN
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}