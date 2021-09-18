<?php

namespace App\Models;

use App\Exceptions\PageParamsInvalidException;
use Closure;
use EloquentFilter\Filterable;
use Exception;
use http\Exception\InvalidArgumentException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

/**
 * @method static Builder filter(array $filter)
 */
trait ModelTrait
{
    use Filterable;

    static function page($mixin = null, $columns = ['*'])
    {
        /** @var FormRequest $request */
        $request  = $mixin instanceof FormRequest ? $mixin : app('request');
        $page     = $request->input('page') ?? 1;
        $pageSize = $request->input('page_size') ?? 20;
        if (!is_numeric($page) || !is_numeric($pageSize) || $pageSize > 5000) {
            throw new PageParamsInvalidException();
        }
        /** @var Builder $builder */
        $builder = null;
        if ($mixin instanceof Closure) {
            /** @var Builder $builder */
            $builder = $mixin();
        } else if ($builder === null) {
            $builder = static::filter($request->validated());
        } else {
            throw new Exception();
        }
        return $builder->paginate(
            $pageSize,
            $columns,
            'page',
            $page
        );
    }
}