<?php

namespace Tests\Unit\Dev;

use App\Models\Dev\DevCategory;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    protected $data = [
        'pid'  => 1,
        'name' => '测试',
    ];

    protected $baseUrl = '/dev/category';

    function testFind()
    {
        $url = 'find/' . DevCategory::all()->random()->max('id');
        $this->post($url);
    }

    function testList()
    {
        $url  = 'list';
        $data = [
            'code' => 'fl'
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
        $data = ['ids' => [DevCategory::query()->max('id')]];
        $this->delete($url, $data);
    }

    function testUpdate()
    {
        $url          = DevCategory::query()->max('id');
        $data         = $this->data;
        $data['name'] = $data['name'] . '修改';
        $this->put($url, $data);
    }
}