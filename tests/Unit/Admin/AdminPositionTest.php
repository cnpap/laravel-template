<?php

namespace Tests\Unit\Admin;

use App\Models\Admin\AdminPosition;
use Tests\TestCase;

class AdminPositionTest extends TestCase
{
    protected $data = [
        'name'                 => '测试岗位',
        'admin_department_id'  => '999',
        'admin_permission_ids' => ['1', '2', '3']
    ];

    protected $baseUrl = '/admin/adminPosition';

    function testFind()
    {
        $url = 'find/' . AdminPosition::all()->random()->max('id');
        $this->post($url);
    }

    function testList()
    {
        $url  = 'list';
        $data = [
            'code' => 'gw'
        ];
        $this->post($url, $data);
    }

    /**
     * @depends testDelete
     */
    function testCreate()
    {
        $url  = 'create';
        $data = $this->data;
        $this->post($url, $data);
    }

    function testDelete()
    {
        $url  = 'delete';
        $data = ['ids' => [AdminPosition::query()->max('id')]];
        $this->delete($url, $data);
    }

    function testUpdate()
    {
        $url          = AdminPosition::query()->max('id');
        $data         = $this->data;
        $data['name'] = $data['name'] . '修改';
        $this->put($url, $data);
    }
}
