<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminRoleIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'     => 'string|between:1,40',
            'code'     => 'string|between:1,40',
            'status'   => 'array|max:4',
            'status.*' => 'string|in:' . STATUS_JOIN,
            'detect'   => 'string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
