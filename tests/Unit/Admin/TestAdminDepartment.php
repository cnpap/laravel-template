<?php

namespace Tests\Unit\Admin;

use App\Models\Admin\AdminDepartment;
use Tests\TestCase;

class TestAdminDepartment extends TestCase
{
    const data = [
        'name' => '部门',
    ];

    function testFind()
    {
        $response = $this->post('/admin/department/find/' . AdminDepartment::query()->max('id'), self::data);
        self::log('admin department find', $response->getContent());
        $response->assertStatus(200);
    }

    function testList()
    {
        $response = $this->post('/admin/department/list', [
            'name' => '部门'
        ]);
        self::log('admin department list', $response->getContent());
        $response->assertStatus(200);
    }

    /**
     * @depends testDelete
     */
    function testCreate()
    {
        $data     = [
            'name' => '部门' . rand(1000, 9999)
        ];
        $response = $this->post('/admin/department/create', $data);
        $this->assertSS($response);
    }

    function testDelete()
    {
        $data     = [
            'ids' => [AdminDepartment::query()->max('id')]
        ];
        $response = $this->delete('/admin/department/delete', $data);
        $this->assertSS($response);
    }

    function testUpdate()
    {
        $data     = [
            'name' => '部门' . rand(1000, 9999)
        ];
        $response = $this->put('/admin/department/' . AdminDepartment::query()->max('id'), $data);
        $this->assertSS($response);
    }
}
