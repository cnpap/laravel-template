<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminDepartmentEditRequest;
use App\Http\Requests\Admin\AdminDepartmentIndexRequest;
use App\Models\Admin\AdminDepartment;

class AdminDepartmentController extends Controller
{
    function positions()
    {
        $departments = AdminDepartment::query()
            ->with('position:id,name')
            ->get();
        return ss($departments);
    }

    function find($id)
    {
        $Department = AdminDepartment::query()->findOrFail($id);
        return ss($Department);
    }

    function list(AdminDepartmentIndexRequest $request)
    {
        $result = AdminDepartment::page($request);
        return ss($result);
    }

    function create(AdminDepartmentEditRequest $request)
    {
        $Department     = new AdminDepartment($request->validated());
        $Department->id = uni();
        $Department->save();
        return ss();
    }

    function update(AdminDepartmentEditRequest $request, $id)
    {
        AdminDepartment::query()->where('id', $id)->update($request->validated());
        return ss();
    }

    function delete($id)
    {
        AdminDepartment::clear($id);
        return ss();
    }
}
