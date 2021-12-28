<?php

namespace App\ModelFilters\Admin;

use App\ModelFilters\ModelFilter;
use App\Models\Admin\AdminPosition;

/** @mixin AdminPosition */
class AdminPositionFilter extends ModelFilter
{
    function name($val)
    {
        return $this->where('name', 'like', "%$val%");
    }

    function code($val)
    {
        return $this->where('code', 'like', "%$val%");
    }

    function adminDepartment($val)
    {
        return $this->where('admin_department_id', $val);
    }
}
