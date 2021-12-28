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
        $num         = rand(1000, 9999);
        $name        = '岗位' . $num;
        $code        = fnPinYin($name);
        $description = '岗位描述/备注' . $num;
        $department  = AdminDepartment::all()->random();
        return [
            'id'                  => uni(),
            'admin_department_id' => $department,
            'status'              => _USED,
            'name'                => $name,
            'code'                => $code,
            'description'         => $description
        ];
    }
}
