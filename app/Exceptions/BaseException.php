<?php

namespace App\Exceptions;

class BaseException extends \RuntimeException
{
    public $customCode;
    public $messageType;

    function __construct($message = "", $customCode = 0, $code = 500, $messageType = 'error')
    {
        $this->customCode = $customCode;
        $this->messageType = $messageType;
        parent::__construct($message, $code);
    }
}
