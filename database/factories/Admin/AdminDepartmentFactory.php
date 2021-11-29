<?php

namespace Database\Factories\Admin;

use App\Models\Admin\AdminDepartment;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminDepartmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AdminDepartment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $num         = rand(1000, 9999);
        $name        = '部门' . $num;
        $code        = fnPinYin($name);
        $description = '部门描述/备注' . $num;
        return [
            'id'          => $this->faker->unique()->numberBetween(100000, 999999),
            'status'      => _USED,
            'name'        => $name,
            'code'        => $code,
            'description' => $description
        ];
    }
}
