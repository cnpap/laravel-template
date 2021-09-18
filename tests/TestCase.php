<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\TestResponse;
use RuntimeException;
use function App\Http\Controllers\ss;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    static function log($method, $data)
    {
        if (!$data) {
            return;
        }
        if (is_string($data)) {
            $data = json_decode($data, true);
            if (!$data) {
                throw new RuntimeException('debug 数据不是 json 数据');
            }
        }
        Log::channel('dev')->debug($method, $data);
    }

    function assertSS($data)
    {
        if ($data instanceof TestResponse) {
            $data = $data->getContent();
        }
        $this->assertEquals('{"status":200}', $data);
    }
}