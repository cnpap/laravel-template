<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminDepartmentEditRequest;
use App\Http\Requests\Admin\AdminDepartmentIndexRequest;
use App\Models\Admin\AdminDepartment;

class AdminDepartmentController extends Controller
{
    protected $model = AdminDepartment::class;

    function positions()
    {
        $ones = AdminDepartment::query()
            ->with('positions:id,name')
            ->get();
        return ss($ones);
    }

    function list(AdminDepartmentIndexRequest $request)
    {
        $result = AdminDepartment::indexFilter($request->validated())
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
        AdminDepartment::clearCacheOptions();
        return ss();
    }

    function update(AdminDepartmentEditRequest $request, $id)
    {
        $post = $request->validated();
        mergeCode($post);
        AdminDepartment::query()->where('id', $id)->update($post);
        AdminDepartment::clearCacheOptions();
        return ss();
    }
}
