<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminRoleIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status'   => 'array|max:4',
            'status.*' => 'string|in:' . STATUS_JOIN,
            'name'     => 'string|between:1,40',
            'coed'     => 'string|between:1,40'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
