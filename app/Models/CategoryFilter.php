<?php

namespace App\Models;

use EloquentFilter\ModelFilter;

class CategoryFilter extends ModelFilter
{
    function name($val)
    {
        return $this->where('name', 'like', "%$val%");
    }
}
