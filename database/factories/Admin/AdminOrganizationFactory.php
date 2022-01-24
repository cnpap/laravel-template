<?php

namespace Database\Factories\Admin;

use App\Models\Admin\AdminOrganization;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminOrganizationFactory extends Factory
{
    protected $model = AdminOrganization::class;

    public function definition(): array
    {
        $num         = rand(1000, 9999);
        $name        = '组织' . $num;
        $code        = fnPinYin($name);
        $description = '组织描述/备注' . $num;
        return [
            'status'      => _USED,
            'name'        => $name,
            'code'        => $code,
            'description' => $description
        ];
    }
}
