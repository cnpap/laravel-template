<?php

namespace App\Models\Admin;

use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int id
 * @property int status
 * @property int admin_department_id
 * @property string name
 * @property string description
 *
 * relation
 *
 * @property AdminDepartment adminDepartment
 */
class AdminPositionFilter extends ModelFilter
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
