<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @property string $name
 * @property Model $model
 */
abstract class CategoryFactory extends Factory
{
    static $keys = [];

    public function definition(): array
    {
        $id  = uni();
        $pid = null;
        $num = rand(1000, 9999);
        if (count(self::$keys)) {
            $pid = rand(1, 10) > 2 ? collect(self::$keys)->random() : null;
        }
        $name = '分类名称';
        if (property_exists($this, 'name')) {
            $name = $this->name;
        }
        $name         .= $num;
        $code         = fnPinYin($name);
        self::$keys[] = $id;
        return [
            'id'   => $id,
            'pid'  => $pid,
            'name' => $name,
            'code' => $code,
        ];
    }
}
