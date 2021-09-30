<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class AdminUserEditRequest extends FormRequest
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
            'username'          => 'required|string',
            'password'          => 'string',
            'admin_position_id' => 'required',
            'phone'             => 'required',
            'email'             => 'string|email',
            'gender'            => 'string|in:' . GENDER_JOIN,
            'status'            => 'string|in:' . STATUS_JOIN,
        ];
    }
}
