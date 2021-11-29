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
            'username'          => 'required|string|between:1,40',
            'code'              => 'string|between:1,40',
            'password'          => 'string|between:250,450',
            'admin_position_id' => 'required|string|id',
            'phone'             => 'required|string|phone',
            'email'             => 'string|email',
            'gender'            => 'required|string|in:' . GENDER_JOIN,
            'status'            => 'string|in:' . STATUS_JOIN,
        ];
    }
}
