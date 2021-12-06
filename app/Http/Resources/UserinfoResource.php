<?php

namespace App\Http\Resources;

use App\Models\Admin\AdminRolePermissionName;
use App\Models\Admin\AdminUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/** @mixin AdminUser */
class UserinfoResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'admin_position_id' => $this->admin_position_id,
            'username'          => $this->username,
            'gender'            => $this->gender,
            'avatar'            => $this->avatar,
            'status'            => $this->status,
            'phone'             => $this->phone,
            'email'             => $this->email,
            'code'              => $this->code,
            'admin_position'    => [
                'id'          => $this->admin_position->id,
                'name'        => $this->admin_position->name,
                'description' => $this->admin_position->description,
            ],
            'admin_department'  => [
                'id'   => $this->admin_position->admin_department->id,
                'name' => $this->admin_position->admin_department->name,
            ],
        ];
    }
}
