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

    function name($val)
    {
        return $this->where('name', 'like', "%$val%");
    }

    function code($val)
    {
        return $this->where('code', 'like', "%$val%");
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
        /** @var Model $model */
        $model        = $this->query->getModel();
        $modelColumns = implode(', ', $model::Fulltext);
        preg_match_all('@[^ ,]+@', $val, $matched);
        $val = $matched[0];
        $val = implode(' +', $val);
        $val = '+' . $val;
        return $this->whereRaw("match($modelColumns) against('$val' in boolean mode)");
    }
}
