<?php

namespace Database\Factories\Dev;

use App\Models\Dev\DevCategory;
use Database\Factories\CategoryFactory;

class DevCategoryFactory extends CategoryFactory
{
    protected $model = DevCategory::class;
    protected $name  = '测试分类';
}
