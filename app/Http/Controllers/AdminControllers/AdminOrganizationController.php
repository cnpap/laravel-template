<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminOrganizationEditRequest;
use App\Http\Requests\Admin\AdminOrganizationIndexRequest;
use App\Models\Admin\AdminOrganization;

class AdminOrganizationController extends Controller
{
    protected $model = AdminOrganization::class;

    function lock($id)
    {
        AdminOrganization::enabled()
            ->where('id', $id)
            ->firstOrFail();
        session(['adminOrganizationId' => $id]);
        return ss();
    }

    function positions()
    {
        $ones = AdminOrganization::query()
            ->with('positions:id,name')
            ->get();
        return ss($ones);
    }

    function find($id)
    {
        /** @var AdminOrganization $one */
        $one = AdminOrganization::query()
            ->select([
                'id',
                'status',
                'name',
            ])
            ->where('id', $id)
            ->firstOrFail();
        return result($one);
    }

    function list(AdminOrganizationIndexRequest $request)
    {
        $result = AdminOrganization::indexFilter($request->validated())
            ->paginate(...usePage());
        return page($result);
    }

    function create(AdminOrganizationEditRequest $request)
    {
        $post = $request->validated();
        mergeCode($post);
        $one = new AdminOrganization($post);
        $one->save();
        AdminOrganization::clearCacheOptions();
        return ss();
    }

    function update(AdminOrganizationEditRequest $request, $id)
    {
        $post = $request->validated();
        mergeCode($post);
        AdminOrganization::query()->where('id', $id)->update($post);
        AdminOrganization::clearCacheOptions();
        return ss();
    }
}
