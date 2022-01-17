<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\TestResponse;

/**
 * @property string baseUrl
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    function getUri($url)
    {
        $url = '/' . $url;
        if (property_exists($this, 'baseUrl')) {
            return $this->baseUrl . $url;
        }
        return $url;
    }

    public function tidy($uri, $response, $methodType)
    {
        $content = $response->getContent();
        self::log($methodType . $uri, $content);
        $response->assertStatus(200);
    }

    public function post($uri, array $data = [], array $headers = [])
    {
        $uri      = $this->getUri($uri);
        $response = parent::post($uri, $data, $headers);
        $this->tidy($uri, $response, 'POST');
        return $response;
    }

    public function put($uri, array $data = [], array $headers = [])
    {
        $uri      = $this->getUri($uri);
        $response = parent::put($uri, $data, $headers);
        $this->tidy($uri, $response, 'PUT');
        return $response;
    }

    public function delete($uri, array $data = [], array $headers = [])
    {
        $uri      = $this->getUri($uri);
        $response = parent::delete($uri, $data, $headers);
        $this->tidy($uri, $response, 'DELETE');
        return $response;
    }

    static function log($method, $data)
    {
        if (!$data) {
            return;
        }
        if (is_string($data)) {
            $tmp = json_decode($data, true);
            if (!$tmp) {
                $data = [$data];
            } else {
                $data = $tmp;
            }
        }
        Log::channel('dev')->debug($method, $data);
    }

    function assertSS($method, $data)
    {
        if ($data instanceof TestResponse) {
            $data = $data->getContent();
        }
        self::log($method, $data);
        $this->assertEquals('{"code":200}', $data);
    }
}