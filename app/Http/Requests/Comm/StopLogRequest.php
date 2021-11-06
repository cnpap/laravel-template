<?php

namespace App\Http\Requests\Comm;

use Illuminate\Foundation\Http\FormRequest;

class StopLogRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'description' => 'required|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}