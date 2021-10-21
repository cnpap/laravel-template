<?php

namespace Tests\Unit\Admin;

use App\Models\Admin\AdminPosition;
use Tests\TestCase;

class TestAdminPosition extends TestCase
{
    const data = [
        'name'                 => '岗位',
        'admin_department_id'  => '999',
        'admin_permission_ids' => ['1', '2', '3']
    ];

    function testFind()
    {
        $response = $this->post('/admin/position/find/' . AdminPosition::query()->max('id'), self::data);
        self::log('admin position find', $response->getContent());
        $response->assertStatus(200);
    }

    function testList()
    {
        $response = $this->post('/admin/position/list', [
            'name' => '部门'
        ]);
        self::log('admin position list', $response->getContent());
        $response->assertStatus(200);
    }

    /**
     * @depends testDelete
     */
    function testCreate()
    {
        $data         = self::data;
        $data['name'] .= rand(1000, 9999);
        $response     = $this->post('/admin/position/create', $data);
        $this->assertSS($response);
    }

    function testDelete()
    {
        $response = $this->delete('/admin/position/delete', ['ids' => AdminPosition::query()->max('id')]);
        $this->assertSS($response);
    }

    function testUpdate()
    {
        $data         = self::data;
        $data['name'] .= rand(1000, 9999);
        $response     = $this->put('/admin/position/' . AdminPosition::query()->max('id'), $data);
        $this->assertSS($response);
    }
}
