<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

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
            'code'              => 'nullable|string|between:1,40',
            'password'          => 'nullable|string|between:250,450',
            'admin_position_id' => 'required|int',
            'admin_role_ids'    => 'required|array|min:1',
            'admin_role_ids.*'  => 'required|int',
            'phone'             => 'required|string|phone',
            'email'             => 'nullable|string|email',
            'gender'            => 'required|int|in:' . GENDER_JOIN,
            'status'            => 'nullable|int|in:' . STATUS_JOIN,
            'description'       => 'nullable|string|max:200',
        ];
    }
}
