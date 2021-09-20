<?php

namespace App\Models\Admin;

use App\Models\Model;

/**
 * @property int admin_position_id
 * @property int admin_permission_id
 */
class AdminPositionPermission extends Model
{
    protected $table = 'admin_position_permission';
}
