<?php

namespace Tests\Unit\Admin;

use App\Models\Admin\AdminUser;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    protected $data = [
        'username'          => '测试',
        'admin_position_id' => 999,
        'gender'            => _MAN,
        'status'            => _NEW,
        'phone'             => '13355557777',
        'admin_role_ids'    => [1, 2, 3],
    ];

    protected $baseUrl = '/admin/adminUser';

    function testFind()
    {
        $url = 'find/' . AdminUser::all()->random()->max('id');
        $this->post($url);
    }

    function testList()
    {
        $url  = 'list';
        $data = [
            'username' => '管理'
        ];
        $this->post($url, $data);
    }

    /**
     * @depends testDelete
     */
    function testCreate()
    {
        $url              = 'create';
        $data             = $this->data;
        $data['phone']    = '1' . rand(3000000000, 9999999999);
        $data['username'] .= '新增' . rand(1000, 9999);
        $this->post($url, $data);
    }

    function testDelete()
    {
        $url  = 'delete';
        $data = ['ids' => [AdminUser::query()->max('id')]];
        $this->delete($url, $data);
    }

    function testUpdate()
    {
        $url              = AdminUser::query()->max('id');
        $data             = $this->data;
        $data['username'] .= '修改' . rand(1000, 9999);
        $this->put($url, $data);
    }

    function testForgotPassword()
    {
        $url      = 'forgotPassword/2';
        $password = rsaEncrypt('1a2B3.__');
        $data     = compact('password');
        $this->post($url, $data);
    }
}
