<?php

namespace App\Models\Admin;

use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;

class AdminPermissionFilter extends ModelFilter
{
    function name($val)
    {
        return $this->when(
            $val,
            function (Builder $builder, $val) {
                $this->where('name', 'like', "%$val%");
            }
        );
    }
}
