<?php

namespace Database\Factories\Admin;

use App\Models\Admin\AdminDepartment;
use App\Models\Admin\AdminPosition;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminPositionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AdminPosition::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $department = AdminDepartment::all()->random();
        return [
            'id'                  => $this->faker->unique()->numberBetween(100000, 999999),
            'admin_department_id' => $department,
            'status'              => USED,
            'name'                => '测试岗位' . $this->faker->unique()->numberBetween(1, 500),
        ];
    }
}
