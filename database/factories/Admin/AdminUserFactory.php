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
    const RAND_username = ['张三', '李四', '王五'];

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        /** @var AdminPosition $position */
        $position = AdminPosition::all()->random();
        return [
            'id'                => $this->faker->unique()->numberBetween(100000, 999999),
            'admin_position_id' => $position->id,
            'status'            => USED,
            'gender'            => [MAN, WOMAN][rand(0, 1)],
            'username'          => self::RAND_username[rand(0, 2)] . $this->faker->unique()->numberBetween(1, 100),
            'phone'             => $this->faker->unique()->numberBetween(13311112222, 19911112222),
            'email'             => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => bcrypt('123456'),
            'remember_token'    => Str::random(10),
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
