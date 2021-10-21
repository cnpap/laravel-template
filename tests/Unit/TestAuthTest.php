<?php

namespace Tests\Unit;

use Tests\TestCase;

class TestAuthTest extends TestCase
{
    function testLogin()
    {
        $phone    = '19977775555';
        $password = '123456';
        $data     = compact('phone', 'password');
        $response = $this->post('/login', $data);
        self::log('login', $response->getContent());
        $response->assertStatus(200);
    }
}