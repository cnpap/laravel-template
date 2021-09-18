<?php

namespace Tests\Admin;

use App\Models\Admin\AdminUser;
use Tests\TestCase;

class TestAdminUser extends TestCase
{
    const data = [
        'real_name' => '真实',
        'nick_name' => '昵称',
        'sex'       => AdminUser::MAN,
        'status'    => OK,
        'phone'     => 13355557777,
        'password'  => '123456',
    ];

    function testFind()
    {
        $response = $this->post('/admin/user/find/' . AdminUser::query()->max('id'), self::data);
        self::log('admin user find', $response->getContent());
        $this->assertEquals(200,$response->getStatusCode());
    }

    function testList()
    {
        $response = $this->post('/admin/user/list', [
            'nick_name' => '管理'
        ]);
        self::log('admin user list', $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @depends testDelete
     */
    function testCreate()
    {
        $data     = self::data;
        $response = $this->post('/admin/user', $data);
        $this->assertSS($response);
    }

    function testDelete()
    {
        $response = $this->delete('/admin/user/' . AdminUser::query()->max('id'));
        $this->assertSS($response);
    }

    function testUpdate()
    {
        $response = $this->put('/admin/user/' . AdminUser::query()->max('id'), self::data);
        $this->assertSS($response);
    }
}