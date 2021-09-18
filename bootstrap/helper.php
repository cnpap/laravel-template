<?php

use Godruoyi\Snowflake\Snowflake;

const YES = 1;
const NO  = 2;

const OK  = 1;
const ERR = 2;

function uni()
{
    $snowflake = new Snowflake();
    return $snowflake->setSequenceResolver(function ($currentTime) {
        static $lastTime;
        static $sequence;

        if ($lastTime == $currentTime) {
            ++$sequence;
        } else {
            $sequence = 0;
        }

        $lastTime = $currentTime;

        return $sequence;
    })->id();
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