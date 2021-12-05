<?php

namespace App\Exceptions;

class BurstException extends BaseException
{
    const INVALID_DATA = '没有获取到预期的数据';
    const DANGER       = '检测到有危险操作';
}
