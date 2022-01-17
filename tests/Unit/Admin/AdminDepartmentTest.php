<?php

namespace Tests\Unit\Admin;

use App\Models\Admin\AdminDepartment;
use Tests\TestCase;

class AdminDepartmentTest extends TestCase
{
    protected $data = [
        'name' => '测试部门',
    ];

    protected $baseUrl = '/admin/adminDepartment';

    function testFind()
    {
        $url = 'find/' . AdminDepartment::all()->random()->max('id');
        $this->post($url);
    }

    function testList()
    {
        $url  = 'list';
        $data = [
            'code' => 'bm'
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
