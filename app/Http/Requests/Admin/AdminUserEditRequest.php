<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\EditRequest;

class AdminUserEditRequest extends EditRequest
{
    protected $table = 'admin_user';

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
            'username'              => [
                'required',
                'string',
                $this->unique('username'),
                'between:1,40'
            ],
            'password'          => 'nullable|string|between:250,450',
            'admin_position_id' => 'required|int',
            'admin_role_ids'    => 'required|array|min:1',
            'admin_role_ids.*'  => 'required|int',
            'phone'             => [
                'required',
                'string',
                'phone',
                $this->unique('phone'),
            ],
            'email'             => 'nullable|string|email',
            'gender'            => 'required|int|in:' . GENDER_JOIN,
            'status'            => 'nullable|int|in:' . STATUS_JOIN,
            'description'       => 'nullable|string|max:200',
        ];
    }
}
