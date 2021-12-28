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

        $username = '真实名称z';
        /** @var AdminPosition $position */
        $position                 = AdminPosition::all()->random();
        $super                    = new AdminUser();
        $super->admin_position_id = $position->id;
        $super->id                = '_super_manager';
        $super->gender            = _MAN;
        $super->status            = _USED;
        $super->username          = $username;
        $super->code              = fnPinYin($username);
        $super->phone             = '19977775555';
        $super->email             = 'sia-fl@outlook.com';
        $super->password          = bcrypt('123456');
        $super->save();
        AdminUser::factory()->count(30)->create();
    }
}
