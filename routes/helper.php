<?php

use App\Exceptions\BaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryEditRequest;
use App\Http\Requests\CategoryIndexRequest;
use App\Models\Comm\Category;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


function routePrefix($prefix, $callback)
{
    Route::prefix($prefix)->group($callback);
}

function routePack($prefix, $control, $methods = null)
{
    /** @var Controller $control */
    $beforeCallback = function () use (
        $control,
        $methods
    ) {
        if ($methods !== null) {
            foreach ($methods as $index => $method) {
                $template = null;
                if (!is_int($index)) {
                    $template = $method;
                    $method   = $index;
                }
                $path = $method;
                if ($template !== null) {
                    $path = str_replace('$', $path, $template);
                }
                if (!method_exists($control, $method)) {
                    throw new BaseException;
                }
                Route::post($path, [$control, $method]);
            }
        }
        if (method_exists($control, 'find')) {
            Route::post('/find/{id}', [$control, 'find']);
        }
        if (method_exists($control, 'list')) {
            Route::post('/list', [$control, 'list']);
        }
        if (method_exists($control, 'create')) {
            Route::post('/create', [$control, 'create']);
        }
        if (method_exists($control, 'update')) {
            Route::put('/{id}', [$control, 'update']);
        }
        if (method_exists($control, 'delete')) {
            Route::delete('/delete', [$control, 'delete']);
        }
        if (method_exists($control, 'status')) {
            Route::post('/status', [$control, 'status']);
        }
    };
    if ($prefix) {
        routePrefix($prefix, $beforeCallback);
    } else {
        $beforeCallback();
    }
}

function routePackCategory($prefix, string $model)
{
    /** @var Category $model */
    Route::group(['prefix' => $prefix], function () use ($model) {
        Route::post(
            '/find/{id}',
            function ($id) use ($model) {
                /** @var Category $one */
                $one = $model::query()
                    ->select([
                        'id',
                        'pid',
                        'status',
                        'name',
                        'code',
                        'level',
                        'description',
                    ])
                    ->with('parent')
                    ->where('id', $id)
                    ->firstOrFail();
                if ($one->parent) {
                    $one->p_name = $one->parent->name;
                } else {
                    $one->p_name = '顶级分类';
                }
                return result($one);
            }
        );
        Route::post(
            '/list',
            function (CategoryIndexRequest $request) use ($model) {
                $many = $model::indexFilter($request->validated())
                    ->select([
                        'id',
                        'pid',
                        'status',
                        'name',
                        'code',
                        'level',
                        'created_at',
                        'updated_at'
                    ])
                    ->paginate(...usePage());
                return page($many);
            }
        );
        Route::post(
            '/create',
            function (CategoryEditRequest $request) use ($model) {
                $post = $request->validated();
                mergeCode($post);
                $sub = new $model($post);
                $ok  = (new Category())->getConnection()->transaction(function () use ($sub, $model) {
                    /** @var Category $sub */
                    if ($sub->pid) {
                        /** @var Category $pSub */
                        $pSub = $model::query()->where('id', $sub->pid)->firstOrFail();
                        if ($sub->leafLevel <= $pSub->level) {
                            throw new BaseException();
                        }
                        $sub->level = $pSub->level + 1;
                        $model::query()->where('id', $sub->pid)->where('status', _NEW)->update(['status' => _USED]);
                    } else {
                        $sub->pid   = null;
                        $sub->level = 1;
                    }
                    $ok = $sub->save();
                    if (!$ok) {
                        throw new RuntimeException();
                    }
                    return true;
                });
                return tx($ok);
            }
        );
        Route::put(
            '/{id}',
            function (CategoryEditRequest $request, $id) use ($model) {
                $model::query()->where('id', $id)->update($request->validated());
                return ss();
            }
        );
        Route::delete(
            '/delete',
            function () use ($model) {
                $model::del();
                return ss();
            }
        );
        Route::post(
            '/status',
            function (Request $request) use ($model) {
                $ids    = $request->input('ids');
                $status = $request->input('status');
                /** @var Category $re */
                $re = new $model;
                $ok = $re->getConnection()->transaction(function () use ($re, $ids, $status) {
                    for ($i = 0; $i < $re->leafLevel; $i++) {
                        if (count($ids) > 1) {
                            $re::staticQuery($ids)->where('status', '!=', _NEW)->update(['status' => $status]);
                            $ids = $re::query()->whereIn('pid', $ids)->select(['id'])->get()->pluck('id');
                        } else {
                            break;
                        }
                    }
                    return true;
                });
                return tx($ok);
            }
        );
        Route::post(
            '/tree',
            function () use ($model) {
                $item = $model
                    ::query()
                    ->select([
                        'id',
                        'pid',
                        'name',
                        'code'
                    ])
                    ->whereIn('status', [_USED, _NEW])
                    ->with('parent')
                    ->get();
                return result(treeOptions($item));
            }
        );
    });
}
