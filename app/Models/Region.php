<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperRegion
 */
class Region extends Model
{
    protected $connection = 'region';

    protected $table = 'region';

    function children()
    {
        return $this->hasMany(Region::class, 'parent_id', 'id');
    }

    function parent()
    {
        return $this->hasOne(Region::class, 'id', 'parent_id');
    }
}