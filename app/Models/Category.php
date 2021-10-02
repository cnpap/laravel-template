<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperCategory
 * @property string status
 * @property string pid
 * @property string name
 * @property string description
 *
 * @property Category $parent
 */
class Category extends Model
{
    use HasFactory;

    function parent()
    {
        return $this->hasOne(static::class, 'id', 'pid');
    }

    function modelFilter()
    {
        return $this->provideFilter(CategoryFilter::class);
    }
}