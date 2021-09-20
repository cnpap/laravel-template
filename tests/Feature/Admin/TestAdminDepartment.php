<?php

namespace Tests\Feature\Admin;

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
        $data     = self::data;
        $response = $this->post('/admin/department', $data);
        $this->assertSS($response);
    }

    function testDelete()
    {
        $response = $this->delete('/admin/department/' . AdminDepartment::query()->max('id'));
        $this->assertSS($response);
    }

    function testUpdate()
    {
        $response = $this->put('/admin/department/' . AdminDepartment::query()->max('id'), self::data);
        $this->assertSS($response);
    }
}
