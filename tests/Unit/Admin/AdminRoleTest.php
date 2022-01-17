<?php

namespace Tests\Unit\Admin;

use App\Models\Admin\AdminDepartment;
use Tests\TestCase;

class AdminRoleTest extends TestCase
{
    protected $data = [
        'name' => '测试角色',
    ];

    protected $baseUrl = '/admin/adminRole';

    function testFind()
    {
        $url = 'find/' . AdminDepartment::all()->random()->max('id');
        $this->post($url);
    }

    function testList()
    {
        $url  = 'list';
        $data = [
            'code' => 'js'
        ];
        $this->post($url, $data);
    }

    /**
     * @depends testDelete
     */
    function testCreate()
    {
        $url          = 'create';
        $data         = $this->data;
        $data['name'] .= '新增' . rand(1000, 9999);
        $this->post($url, $data);
    }

    function testDelete()
    {
        $url  = 'delete';
        $data = ['ids' => [AdminDepartment::query()->max('id')]];
        $this->delete($url, $data);
    }

    function testUpdate()
    {
        $url          = AdminDepartment::query()->max('id');
        $data         = $this->data;
        $data['name'] .= '修改' . rand(1000, 9999);
        $this->put($url, $data);
    }
}
