<?php

namespace Database\Factories\Admin;

use App\Models\Admin\AdminPosition;
use App\Models\Admin\AdminUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AdminUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AdminUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $num         = rand(1000, 9999);
        $name        = '管理员' . $num;
        $code        = fnPinYin($name);
        $description = '管理员备注/描述' . $num;
        /** @var AdminPosition $position */
        $position = AdminPosition::all()->random();
        return [
            'admin_position_id' => $position->id,
            'status'            => _NEW,
            'gender'            => [_MAN, _WOMAN][rand(0, 1)],
            'username'          => $name,
            'code'              => $code,
            'phone'             => $this->faker->unique()->numberBetween(13311112222, 19911112222),
            'email'             => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => bcrypt('123456'),
            'remember_token'    => Str::random(10),
            'description'       => $description
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
