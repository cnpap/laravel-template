<?php

namespace App\Models\Admin;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int id
 * @property int status
 * @property string name
 * @property string description
 */
class AdminDepartment extends Model
{
    use HasFactory;

    protected $table = 'admin_department';

    protected $hidden = ['status'];

    function modelFilter()
    {
        return $this->provideFilter(AdminDepartmentFilter::class);
    }
}
