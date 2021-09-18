<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class PageParamsInvalidException extends Exception
{
    function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $message = $message ?? 'Not the expected request format';
        parent::__construct($message, $code, $previous);
    }
}
