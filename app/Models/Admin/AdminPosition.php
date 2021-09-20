<?php

namespace App\Models\Admin;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int id
 * @property int status
 * @property int admin_department_id
 * @property string name
 * @property string description
 *
 * relation
 *
 * @property AdminDepartment adminDepartment
 */
class AdminPosition extends Model
{
    use HasFactory;

    protected $table = 'admin_position';

    function modelFilter()
    {
        return $this->provideFilter(AdminPositionFilter::class);
    }

    function adminDepartment()
    {
        return $this->hasOne(AdminDepartment::class, 'id', 'admin_department_id')->select(['id', 'name']);
    }

    function adminPermissions()
    {
        return $this->belongsToMany(AdminPermission::class, AdminPositionPermission::class, 'admin_permission_id', 'admin_position_id');
    }
}
