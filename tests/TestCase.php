<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\TestResponse;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    static function log($method, $data)
    {
        if (!$data) {
            return;
        }
        if (is_string($data)) {
            $tmp = json_decode($data, true);
            if (!$tmp) {
                throw new RuntimeException('debug 数据不是 json 数据');
            } else {
                $data = $tmp;
            }
        }
        Log::channel('dev')->debug($method, $data);
    }

    function assertSS($data)
    {
        if ($data instanceof TestResponse) {
            $data = $data->getContent();
        }
        $this->assertEquals('{"code":200}', $data);
    }
}