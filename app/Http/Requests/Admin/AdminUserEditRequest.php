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
        $rules = [
            'username'          => 'required|string',
            'password'          => 'string',
            'admin_position_id' => 'required',
            'phone'             => 'required',
            'email'             => 'string|email',
            'gender'            => 'required',
        ];
        /** @var Request $request */
        $request = app('request');
        if ($request->method === 'PUT') {
            $rules['status'] = 'required';
        }
        return $rules;
    }
}
