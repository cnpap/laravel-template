<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryEditRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pid'         => 'string|nullable',
            'status'      => 'string|in:' . STATUS_JOIN,
            'name'        => 'required',
            'description' => 'string|nullable'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}