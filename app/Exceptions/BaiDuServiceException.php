<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class BaiDuServiceException extends HttpException
{
    const TOKEN = 1;
    const OCR   = 2;

    public function __construct($message = null, \Throwable $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct(500, $message, $previous, $headers, $code);
    }
}