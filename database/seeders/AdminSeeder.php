<?php

namespace Database\Seeders;

use App\Models\Admin\AdminDepartment;
use App\Models\Admin\AdminPosition;
use App\Models\Admin\AdminRole;
use App\Models\Admin\AdminUser;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AdminDepartment::factory()->count(5)->create();
        AdminPosition::factory()->count(40)->create();
        AdminRole::factory()->count(15)->create();
        AdminUser::factory()->count(15)->create();
    }
}
