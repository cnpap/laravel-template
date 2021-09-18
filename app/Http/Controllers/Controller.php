<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

// status success
function ss($data = [])
{
    $data['status'] = 200;
    return response($data);
}

// status error
function se($data = [])
{
    $data['status']  = $data['status'] ?? 500;
    $data['message'] = '请求失败请重试';
    return response($data, $data['status']);
}

// transaction
function tx($ok, $onOk = null, $onFail = null)
{
    if ($ok) {
        if ($onOk) {
            $onOk();
        }
        return ss();
    }
    if ($onFail) {
        $onFail();
    }
    return se();
}