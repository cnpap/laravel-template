<?php

namespace App\Models\Admin;

use App\Models\Model;

/**
 * @mixin IdeHelperAdminPermission
 */
class AdminPermission extends Model
{
    protected $table = 'admin_permission';

    const P_DASHBOARD        = 'dashboard';
    const P_SYSTEM           = 'system';
    const P_ADMIN_USER       = 'admin user';
    const P_ADMIN_DEPARTMENT = 'admin department';
    const P_ADMIN_POSITION   = 'admin position';

    function modelFilter()
    {
        return $this->provideFilter(AdminPermissionFilter::class);
    }
}
