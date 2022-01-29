<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\EditRequest;

class AdminRoleEditRequest extends EditRequest
{
    public $table = 'admin_role';

    public function rules(): array
    {
        return [
            'status'      => 'nullable|int|in:' . STATUS_JOIN,
            'name'        => [
                'required',
                'string',
                $this->unique(),
                'between:1,40'
            ],
            'description' => 'nullable|string|between:1,200',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
