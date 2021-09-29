<?php

namespace App\Models\Admin;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperAdminDepartment
 */
class AdminDepartment extends Model
{
    use HasFactory;

    protected $table = 'admin_department';

    protected $hidden = ['status'];

    function modelFilter()
    {
        return $this->provideFilter(AdminDepartmentFilter::class);
    }

    function positions()
    {
        return $this->hasMany(AdminPosition::class, 'admin_department_id', 'id');
    }
}
