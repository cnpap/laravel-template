<?php

namespace App\Models\Admin;

use App\ModelFilters\Admin\AdminRoleFilter;
use App\Models\Model;

/**
 * @mixin IdeHelperAdminRole
 */
class AdminRole extends Model
{
    protected $table = 'admin_role';

    function modelFilter()
    {
        return $this->provideFilter(AdminRoleFilter::class);
    }

    function permissions()
    {
        return $this->belongsToMany(AdminPermission::class, AdminRolePermission::class, 'admin_role_id', 'admin_permission_id');
    }
}
