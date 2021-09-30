<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminPositionEditRequest extends FormRequest
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
            'name'                   => 'required',
            'admin_department_id'    => 'required',
            'status'                 => 'string|in:' . STATUS_JOIN,
            'admin_permission_ids'   => 'required|array',
            'admin_permission_ids.*' => 'integer'
        ];
    }
}
