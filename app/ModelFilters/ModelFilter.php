<?php

namespace App\ModelFilters;

use App\Models\Model;
use EloquentFilter\ModelFilter as Base;

/** @mixin Model */
class ModelFilter extends Base
{
    function id($val)
    {
        return $this->where('id', 'like', "%$val%");
    }

    function createdAt($val)
    {
        return $this
            ->where('created_at', '>', $val[0])
            ->where('created_at', '<', $val[1]);
    }

    function updatedAt($val)
    {
        return $this
            ->where('updated_at', '>', $val[0])
            ->where('updated_at', '<', $val[1]);
    }

    function detect($val)
    {
        return $this
            ->where('id', $val)
            ->orWhere('name', 'like', "%$val%")
            ->orWhere('code', 'like', "%$val%");
    }
}
