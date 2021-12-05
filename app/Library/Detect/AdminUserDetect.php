<?php

namespace App\Library\Detect;

use App\Models\Admin\AdminUser;

class AdminUserDetect
{
    function filter($mark)
    {
        $many = AdminUser::query()->where('id', $mark)->get();
        if ($many->count() > 0) {
            return $many;
        }
        $many = AdminUser::query()
            ->where('username', 'like', "%$mark%")
            ->orWhere('phone', 'like', "%$mark%")
            ->limit(10)
            ->get();
        return $many;
    }
}
