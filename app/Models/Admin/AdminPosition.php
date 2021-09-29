<?php

namespace App\Models\Admin;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperAdminPosition
 */
class AdminPosition extends Model
{
    use HasFactory;

    protected $table = 'admin_position';

    function modelFilter()
    {
        return $this->provideFilter(AdminPositionFilter::class);
    }

    function department()
    {
        return $this->hasOne(AdminDepartment::class, 'id', 'admin_department_id');
    }

    function permissions()
    {
        return $this->belongsToMany(AdminPermission::class, AdminPositionPermission::class, 'admin_permission_id', 'admin_position_id');
    }
}
