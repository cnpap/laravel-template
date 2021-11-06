<?php

namespace App\Models\Comm;

use App\Models\Model;

/**
 * @mixin IdeHelperStopLog
 */
class StopLog extends Model
{
    protected $table = 'stop_log';

    const UPDATED_AT = null;
}