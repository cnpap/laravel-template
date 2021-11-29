<?php

namespace App\Http\Resources;

use App\Models\Admin\AdminPermission;
use App\Models\Admin\AdminPositionPermission;
use App\Models\Admin\AdminPositionRole;
use App\Models\Admin\AdminRolePermission;
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
            //            'created_at'          => $this->created_at,
            //            'updated_at'          => $this->updated_at,
            'admin_position_id' => $this->admin_position_id,
            'status'            => $this->status,
            'gender'            => $this->gender,
            'avatar'            => $this->avatar,
            'username'          => $this->username,
            'phone'             => $this->phone,
            'email'             => $this->email,
            'position'          => [
                'id'          => $this->position->id,
                'name'        => $this->position->name,
                'description' => $this->position->description,
            ],
            'department'        => [
                'id'   => $this->position->department->id,
                'name' => $this->position->department->name,
            ],
            'permissions'       => AdminPermission::query()
                ->select(['id', 'label', 'name as value'])
                ->whereIn(
                    'id',
                    AdminRolePermission::query()
                        ->select('admin_permission_id')
                        ->whereIn(
                            'admin_role_id',
                            AdminPositionRole::query()
                                ->select('admin_role_id')
                                ->where('admin_position_id', $this->admin_position_id)
                        )
                )
                ->get()
            //            'email_verified_at'   => $this->email_verified_at,
            //            'password'            => $this->password,
            //            'remember_token'      => $this->remember_token,
            //            'notifications_count' => $this->notifications_count,
            //            'tokens_count'        => $this->tokens_count,
        ];
    }
}
