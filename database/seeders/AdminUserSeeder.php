<?php

namespace Database\Seeders;

use App\Models\Admin\AdminUser;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $super            = new AdminUser();
        $super->id        = 1;
        $super->sex       = 1;
        $super->status    = 1;
        $super->nick_name = '超级管理员';
        $super->real_name = '真实名称';
        $super->phone     = '19977775555';
        $super->email     = 'sia-fl@outlook.com';
        $super->password  = bcrypt('123456');
        $super->save();
        AdminUser::factory()->count(100)->create();
    }
}
