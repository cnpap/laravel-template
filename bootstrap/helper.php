<?php

use Godruoyi\Snowflake\Snowflake;

// OK 也代表数据是新增数据
const OK  = 1;
const ERR = 2;

// USED 有过关联的数据, 一般是不可以被删除的
const USED = 99;

class Uni
{
    static $lastTime;
    static $sequence;
}

function uni()
{
    $snowflake = new Snowflake();
    return $snowflake->setSequenceResolver(function ($currentTime) {
        if (Uni::$lastTime == $currentTime) {
            ++Uni::$sequence;
        } else {
            Uni::$sequence = 0;
        }

        Uni::$lastTime = $currentTime;

        return Uni::$sequence;
    })->id();
}

// status success
function ss($data = [])
{
    $data['code'] = 200;
    return response($data);
}

// status error
function se($data = [])
{
    $data['code']    = $data['code'] ?? 500;
    $data['message'] = '请求失败请重试';
    return response($data, $data['code']);
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