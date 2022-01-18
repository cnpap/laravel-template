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

    const Fulltext = [
        'name',
        'code'
    ];

    function modelFilter()
    {
        return $this->provideFilter(AdminRoleFilter::class);
    }

    function permission_name()
    {
        return $this->hasMany(AdminRolePermissionName::class, 'admin_role_id', 'id');
    }
}
