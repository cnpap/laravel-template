<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryEditRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pid'         => 'string|nullable|id',
            'status'      => 'string|in:' . STATUS_JOIN,
            'name'        => 'required|string|between:1,40',
            'code'        => 'string|between:1,40',
            'description' => 'string|nullable'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
