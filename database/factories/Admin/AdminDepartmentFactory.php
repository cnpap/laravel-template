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
        return [
            'id'     => $this->faker->unique()->numberBetween(100000, 999999),
            'status' => USED,
            'name'   => '测试部门' . $this->faker->unique()->numberBetween(1, 500),
        ];
    }
}
