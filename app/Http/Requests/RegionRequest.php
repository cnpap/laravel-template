<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'baseId' => 'required',
            ''
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}