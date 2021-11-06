<?php

namespace App\Http\Controllers\CommonControllers;

use App\Http\Controllers\Controller;
use App\Models\Comm\StopLog;

class LogController extends Controller
{
    function stopLog($id)
    {
        $one = StopLog::query()->where('subject_id', $id)->first();
        return result($one);
    }
}