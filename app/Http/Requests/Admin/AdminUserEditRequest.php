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
            'nick_name' => 'required|string',
            'real_name' => 'required|string',
            'password'  => 'required|string',
            'phone'     => 'required',
            'email'     => 'string;email',
            'sex'       => 'required',
        ];
        /** @var Request $request */
        $request = app('request');
        if ($request->method === 'PUT') {
            $rules['status'] = 'required';
        }
        return $rules;
    }
}
