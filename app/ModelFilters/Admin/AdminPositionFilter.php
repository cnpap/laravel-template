<?php

namespace App\ModelFilters\Admin;

use App\Models\Admin\AdminDepartment;
use App\Models\Admin\AdminPosition;
use EloquentFilter\ModelFilter;

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
}
