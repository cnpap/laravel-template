<?php

namespace App\Models\Admin;

use App\ModelFilters\Admin\AdminPositionFilter;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperAdminPosition
 */
class AdminPosition extends Model
{
    use HasFactory;

    protected $table = 'admin_position';

    function modelFilter()
    {
        return $this->provideFilter(AdminPositionFilter::class);
    }

    function department()
    {
        return $this->hasOne(AdminDepartment::class, 'id', 'admin_department_id');
    }
}
