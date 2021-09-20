<?php

namespace App\Models\Admin;

use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;

class AdminUserFilter extends ModelFilter
{
    function username($val)
    {
        return $this->when(
            $val,
            function (Builder $builder, $val) {
                $this->where('username', 'like', "%$val%");
            }
        );
    }

    function phone($val)
    {
        return $this->when(
            $val,
            function (Builder $builder, $val) {
                $this->where('phone', 'like', "%$val%");
            }
        );
    }

    function email($val)
    {
        return $this->when(
            $val,
            function (Builder $builder, $val) {
                $this->where('email', 'like', "%$val%");
            }
        );
    }
}