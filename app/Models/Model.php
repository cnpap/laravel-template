<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Base;

/**
 * @mixin IdeHelperModel
 */
class Model extends Base
{
    use ModelTrait, HasFactory;

    protected $guarded = [];

    protected $casts = [
        'id' => 'string'
    ];
}
