<?php

namespace App\Models\Admin;

use EloquentFilter\ModelFilter;

/** @mixin AdminUser */
class AdminUserFilter extends ModelFilter
{
    function username($val)
    {
        return $this->where('username', 'like', "%$val%");
    }

    function phone($val)
    {
        return $this->where('phone', 'like', "%$val%");
    }

    function email($val)
    {
        return $this->where('email', 'like', "%$val%");
    }

    function department($val)
    {
        return $this->whereIn(
            'admin_position_id',
            AdminPosition::query()
                ->select('id')
                ->where('admin_department_id', $val)
        );
    }
}