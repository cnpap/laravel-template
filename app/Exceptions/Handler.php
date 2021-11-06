<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {

        });
    }

    function render($request, Throwable $e)
    {
        if ($e instanceof BaseException) {
            $result                = [];
            $result['message']     = $e->getMessage() . ' ' . $e->customCode;
            $result['messageType'] = $e->messageType;
            $result['code']        = $e->getCode() ?? 500;
            if (config('app.debug')) {
                $result['trace'] = $e->getTrace();
            }
            return se($result);
        }
        return parent::render($request, $e);
    }
}
