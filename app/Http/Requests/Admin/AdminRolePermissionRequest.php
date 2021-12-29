<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminRolePermissionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'names' => 'array|max:300|redis_key'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
