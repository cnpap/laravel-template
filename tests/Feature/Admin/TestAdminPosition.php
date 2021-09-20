<?php

namespace Tests\Feature\Admin;

use App\Models\Admin\AdminPosition;
use Tests\TestCase;

class TestAdminPosition extends TestCase
{
    const data = [
        'name'                 => '岗位',
        'admin_department_id'  => 999,
        'admin_permission_ids' => [1, 2, 3]
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
        $data     = self::data;
        $response = $this->post('/admin/position', $data);
        $this->assertSS($response);
    }

    function testDelete()
    {
        $response = $this->delete('/admin/position/' . AdminPosition::query()->max('id'));
        $this->assertSS($response);
    }

    function testUpdate()
    {
        $response = $this->put('/admin/position/' . AdminPosition::query()->max('id'), self::data);
        $this->assertSS($response);
    }
}
