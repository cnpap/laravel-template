<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminDepartmentEditRequest;
use App\Http\Requests\Admin\AdminDepartmentIndexRequest;
use App\Models\Admin\AdminDepartment;

class AdminDepartmentController extends Controller
{
    function status()
    {
        AdminDepartment::status();
        return ss();
    }

    function positions()
    {
        $ones = AdminDepartment::query()
            ->with('positions:id,name')
            ->get();
        return ss($ones);
    }

    function find($id)
    {
        $one = AdminDepartment::query()->where('id', $id)->firstOrFail();
        return result($one);
    }

    function list(AdminDepartmentIndexRequest $request)
    {
        $result = AdminDepartment::indexFilter($request->all())
            ->paginate(...usePage());
        return page($result);
    }

    function create(AdminDepartmentEditRequest $request)
    {
        $post = $request->validated();
        mergeCode($post);
        $one     = new AdminDepartment($post);
        $one->id = uni();
        $one->save();
        return ss();
    }

    function update(AdminDepartmentEditRequest $request, $id)
    {
        $post = $request->validated();
        mergeCode($post);
        AdminDepartment::query()->where('id', $id)->update($post);
        return ss();
    }

    function delete()
    {
        AdminDepartment::clear();
        return ss();
    }
}
