<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryEditRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pid'         => 'nullable|int',
            'status'      => 'nullable|int|in:' . STATUS_JOIN,
            'name'        => 'required|string|between:1,40',
            'description' => 'nullable|string|nullable'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
