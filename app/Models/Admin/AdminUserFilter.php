<?php

namespace App\Models\Admin;

use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;

class AdminUserFilter extends ModelFilter
{
    function nickName($val)
    {
        return $this->when(
            $val,
            function (Builder $builder, $val) {
                $this->where('nick_name', 'like', "%$val%");
            }
        );
    }

    function realName($val)
    {
        return $this->when(
            $val,
            function (Builder $builder, $val) {
                $this->where('real_name', 'like', "%$val%");
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