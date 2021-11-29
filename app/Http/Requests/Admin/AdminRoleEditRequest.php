<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminRoleEditRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status'                 => 'string|in:' . STATUS_JOIN,
            'name'                   => 'string|between:1,40',
            'code'                   => 'string|between:1,40',
            'description'            => 'string|between:1,200',
            'admin_permission_ids'   => 'array',
            'admin_permission_ids.*' => 'string'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
