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