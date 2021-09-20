<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\AdminUserEditRequest;
use App\Http\Requests\Admin\AdminUserIndexRequest;
use App\Models\Admin\AdminUser;

class AdminUserController extends Controller
{
    function find($id)
    {
        $user = AdminUser::query()->findOrFail($id);
        return ss($user);
    }

    function list(AdminUserIndexRequest $request)
    {
        $result = AdminUser::page($request);
        return ss($result);
    }

    function create(AdminUserEditRequest $request)
    {
        $user           = new AdminUser($request->validated());
        $user->id       = uni();
        $user->password = bcrypt($user->password);
        $user->save();
        return ss();
    }

    function update(AdminUserEditRequest $request, $id)
    {
        AdminUser::query()->where('id', $id)->update($request->validated());
        return ss();
    }

    function delete($id)
    {
        AdminUser::query()->where('id', $id)->delete();
        return ss();
    }
}
