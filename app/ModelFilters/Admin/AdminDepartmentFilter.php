<?php

namespace App\ModelFilters\Admin;

use App\Models\Admin\AdminDepartment;
use EloquentFilter\ModelFilter;

/** @mixin AdminDepartment */
class AdminDepartmentFilter extends ModelFilter
{
    function name($val)
    {
        return $this->where('name', 'like', "%$val%");
    }

    function code($val)
    {
        return $this->where('code', 'like', "%$val%");
    }
}
