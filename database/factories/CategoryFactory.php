<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @property Model $model
 */
abstract class CategoryFactory extends Factory
{
    static $keys = [];

    public function definition(): array
    {
        $id  = uni();
        $pid = null;
        if (count(self::$keys)) {
            $pid = rand(1, 10) > 2 ? collect(self::$keys)->random() : null;
        }
        self::$keys[] = $id;
        return [
            'id'   => $id,
            'pid'  => $pid,
            'name' => $this->faker->title,
        ];
    }
}
