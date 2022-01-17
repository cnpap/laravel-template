<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids'    => 'required|array|min:1|max:30',
            'ids.*'  => 'string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
