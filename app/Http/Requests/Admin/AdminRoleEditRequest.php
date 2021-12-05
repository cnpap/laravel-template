<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminRoleEditRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status'             => 'string|in:' . STATUS_JOIN,
            'name'               => 'required|string|between:1,40',
            'code'               => 'string|between:1,40',
            'description'        => 'string|between:1,200',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
