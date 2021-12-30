<?php

namespace App\Models\Dev;

use App\Models\Comm\Category;

/**
 * @mixin IdeHelperDevCategory
 */
class DevCategory extends Category
{
    protected $table = 'dev_category';
}
