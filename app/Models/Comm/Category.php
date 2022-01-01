<?php

namespace App\Models\Comm;

use App\ModelFilters\Comm\CategoryFilter;
use App\Models\IdeHelperCategory;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 从第几级开始可选
 * @property int $leafLevel
 *
 * @property int $level
 * @property string id
 * @property string status
 * @property string pid
 * @property string name
 * @property string code
 * @property string description
 * @property Category $parent
 * @mixin IdeHelperCategory
 */
class Category extends Model
{
    use HasFactory;

    public $leafLevel = 2;

    function parent()
    {
        return $this->hasOne(static::class, 'id', 'pid');
    }

    function modelFilter()
    {
        return $this->provideFilter(CategoryFilter::class);
    }
}
