<?php

namespace App\Http\Requests\Commodity;

use Illuminate\Foundation\Http\FormRequest;

class PrvCommodityDosageIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status'   => 'array|max:4',
            'status.*' => 'string|in:' . STATUS_JOIN
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}