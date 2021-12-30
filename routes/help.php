<?php

use App\Exceptions\BaseException;
use App\Http\Requests\CategoryEditRequest;
use App\Http\Requests\CategoryIndexRequest;
use App\Models\Comm\Category;
use App\Models\Model;
use ICanBoogie\Inflector;
use Illuminate\Support\Facades\Route;

app()->bind(
    Inflector::class,
    function () {
        return Inflector::get();
    }
);

function routePrefix($prefix, $callback)
{
    Route::prefix($prefix)->group($callback);
}

function routePack($prefix, $control, $methods = null)
{
    $beforeCallback = function () use (
        $control,
        $methods
    ) {
        if ($methods !== null) {
            /** @var Inflector $inflector */
            $inflector = app(Inflector::class);
            foreach ($methods as $index => $method) {
                $template = null;
                if (!is_int($index)) {
                    $template = $method;
                    $method   = $index;
                }
                $path = $inflector->underscore($method);
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
    /** @var Model $model */
    Route::group(['prefix' => $prefix], function () use ($model) {
        Route::post(
            '/find/{id}',
            function ($id) use ($model) {
                $one = $model::query()->where('id', $id)->firstOrFail();
                return result($one);
            }
        );
        Route::post(
            '/list',
            function (CategoryIndexRequest $request) use ($model) {
                $many = $model::indexFilter($request->validated())->paginate(...usePage());
                return page($many);
            }
        );
        Route::post(
            '/create',
            function (CategoryEditRequest $request) use ($model) {
                $post = $request->validated();
                mergeCode($post);
                $one = new $model($post);
                (new Category())->getConnection()->transaction(function () use ($one, $model) {
                    /** @var Category $one */
                    $one->id = uni();
                    $ok      = $one->save();
                    if (!$ok) {
                        throw new RuntimeException();
                    }
                    if ($one->pid) {
                        $child = $one;
                        $keys  = [];
                        for ($i = 0; $i < 5; $i++) {
                            $keys[] = $child->pid;
                            $child  = $child->parent;
                            if (!$child->pid || $child->status !== _NEW) {
                                break;
                            }
                        }
                        $model::query()->whereIn('id', $keys)->where('status', _NEW)->update(['status' => _USED]);
                    }
                });
                return ss();
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
            function () use ($model) {
                $model::status();
                return ss();
            }
        );
        Route::post(
            '/tree',
            function () use ($model) {
                $item = $model
                    ::query()->with('parent')->get();
                return result(treeOptions($item));
            }
        );
    });
}
