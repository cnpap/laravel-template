<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\EditRequest;

class AdminPositionEditRequest extends EditRequest
{
    public $table = 'admin_position';

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
            'name'                => [
                'required',
                'string',
                $this->unique(),
                'between:1,40'
            ],
            'admin_department_id' => 'required|int',
            'status'              => 'nullable|int|in:' . STATUS_JOIN,
        ];
    }
}
