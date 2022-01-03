<?php

namespace App\ModelFilters\Comm;

use App\ModelFilters\ModelFilter;
use App\Models\Comm\Category;

/** @mixin Category */
class CategoryFilter extends ModelFilter
{
    function parents($val)
    {
        return $this->whereIn('pid', $val);
    }
}
