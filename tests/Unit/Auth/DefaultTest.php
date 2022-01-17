<?php

namespace Tests\Unit\Auth;

use Tests\TestCase;

class DefaultTest extends TestCase
{
    function testLogin()
    {
        $phone    = '19977775555';
        $password = rsaEncrypt('123456');
        $data     = compact('phone', 'password');
        $this->post('login', $data);
    }
}