<?php

namespace App\ModelFilters\Comm;

use EloquentFilter\ModelFilter;

class CategoryFilter extends ModelFilter
{
    function name($val)
    {
        return $this->where('name', 'like', "%$val%");
    }
}
