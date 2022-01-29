<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Base;

/**
 * @property int id
 * @property string created_at
 * @property string updated_at
 * @property boolean status
 * @mixin IdeHelperModel
 */
class Model extends Base
{
    use ModelTrait, HasFactory;

    protected $guarded = [];

    const Fulltext = [];
}
