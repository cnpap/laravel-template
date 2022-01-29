<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\EditRequest;

class AdminDepartmentEditRequest extends EditRequest
{
    public $table = 'admin_department';

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
            'name'        => [
                'required',
                'string',
                $this->unique(),
                'between:1,40'
            ],
            'status'      => 'nullable|int|in:' . STATUS_JOIN,
            'description' => 'nullable|string|max:100'
        ];
    }
}
