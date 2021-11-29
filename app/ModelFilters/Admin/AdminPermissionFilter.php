<?php

namespace App\ModelFilters\Admin;

use App\Models\Admin\AdminPermission;
use EloquentFilter\ModelFilter;

/** @mixin AdminPermission */
class AdminPermissionFilter extends ModelFilter
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
