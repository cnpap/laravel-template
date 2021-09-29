<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminUserIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username'      => 'string',
            'phone'         => 'max:12',
            'email'         => 'string',
            'department_id' => 'integer',
            'status'        => 'array|max:4',
            'status.*'      => 'integer|max:100'
        ];
    }
}
