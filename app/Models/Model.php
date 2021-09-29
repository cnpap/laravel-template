<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Base;

/**
 * @mixin IdeHelperModel
 */
class Model extends Base
{
    use ModelTrait;

    protected $guarded = [];
}
