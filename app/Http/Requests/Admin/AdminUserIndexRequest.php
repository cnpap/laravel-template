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
            'username'            => 'string|max:40',
            'code'                => 'string|max:40',
            'phone'               => 'string|max:12',
            'email'               => 'string',
            'admin_department_id' => 'string',
            'created_at'          => 'range_datetime',
            'updated_at'          => 'range_datetime',
        ];
    }
}
