<?php

namespace Tests\Feature\Admin;

use App\Models\Admin\AdminUser;
use Tests\TestCase;

class TestAdminUser extends TestCase
{
    const data = [
        'username'          => '真实',
        'admin_position_id' => 999,
        'gender'            => AdminUser::MAN,
        'status'            => OK,
        'phone'             => 13355557777,
        'password'          => '123456',
    ];

    function testFind()
    {
        $response = $this->post('/admin/user/find/' . AdminUser::query()->max('id'), self::data);
        self::log('admin user find', $response->getContent());
        $response->assertStatus(200);
    }

    function testList()
    {
        $response = $this->post('/admin/user/list', [
            'username' => '管理'
        ]);
        self::log('admin user list', $response->getContent());
        $response->assertStatus(200);
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
