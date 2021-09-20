<?php

namespace App\Models\Admin;

use App\Models\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int id
 * @property int status
 * @property int gender
 * @property int admin_position_id
 * @property string username
 * @property string avatar
 * @property string phone
 * @property string email
 * @property string password
 *
 * relation
 *
 * @property AdminPosition adminPosition
 */
class AdminUser extends User
{
    use HasApiTokens, HasFactory, Notifiable, ModelTrait;

    protected $table = 'admin_user';

    const MAN   = 1;
    const WOMAN = 2;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    function modelFilter()
    {
        return $this->provideFilter(AdminUserFilter::class);
    }

    function adminPosition()
    {
        return $this->hasOne(AdminPosition::class, 'id', 'admin_position_id')->select('id', 'name', 'admin_department_id');
    }

    function adminDepartment()
    {
        return $this->hasOne(AdminPosition::class, 'id', 'admin_position_id')->with(['adminDepartment'])->select('id', 'name', 'admin_department_id');
    }

    function getAdminPermissions()
    {
        return AdminPermission::query()
            ->select(['id', 'label', 'name as value'])
            ->whereIn(
                'id',
                function (Builder $query) {
                    $query
                        ->from(AdminPositionPermission::table())
                        ->select('admin_permission_id')
                        ->where('admin_position_id', $this->admin_position_id);
                }
            )
            ->get();
    }

    function info()
    {
        $info                = collect($this->toArray())->only(['id', 'username', 'phone', 'email', 'gender', 'avatar']);
        $info['department']  = $this->adminPosition->adminDepartment;
        $info['position']    = $this->adminPosition;
        $info['permissions'] = $this->getAdminPermissions();
        return $info;
    }
}
