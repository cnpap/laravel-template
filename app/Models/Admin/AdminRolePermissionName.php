<?php

namespace App\Models\Admin;

use App\Models\Model;

/**
 * @mixin IdeHelperAdminRolePermissionName
 */
class AdminRolePermissionName extends Model
{
    protected $table = 'admin_role_permission_name';

    public $created_at = null;
    public $updated_at = null;
}
