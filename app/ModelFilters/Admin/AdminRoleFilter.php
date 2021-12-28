<?php

namespace App\ModelFilters\Admin;

use App\Models\Admin\AdminRole;
use EloquentFilter\ModelFilter;

/** @mixin AdminRole */
class AdminRoleFilter extends ModelFilter
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
