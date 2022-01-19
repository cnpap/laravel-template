<?php

namespace App\ModelFilters\Admin;

use App\ModelFilters\ModelFilter;
use App\Models\Admin\AdminPosition;
use App\Models\Admin\AdminUser;

/** @mixin AdminUser */
class AdminUserFilter extends ModelFilter
{
    function gender($val)
    {
        return $this->whereIn('gender', $val);
    }

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

    function adminDepartment($val)
    {
        return $this
            ->whereIn(
                'admin_position_id',
                AdminPosition::query()
                    ->where('admin_department_id', $val)
                    ->select('id')
            );
    }
}
