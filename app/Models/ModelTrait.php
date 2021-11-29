<?php

namespace App\Models;

use App\Exceptions\PageParamsInvalidException;
use DateTimeInterface;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

/**
 * @method static Builder filter(array $filter)
 */
trait ModelTrait
{
    use Filterable;

    static function indexFilter(array $filter)
    {
        /** @var Request $request */
        $request     = app('request');
        $status      = $request->input('status', false);
        $orderBy     = $request->input('sortBy', false);
        $orderByDesc = $request->input('sortDirection', 'desc');
        $builder     = static::filter($filter);
        if ($status) {
            $builder->whereIn('status', $status);
        } else {
            $builder->where('status', '!=', _OFF);
        }
        if ($orderByDesc === 'descend') {
            $orderByDesc = 'desc';
        } else if ($orderByDesc === 'ascend') {
            $orderByDesc = 'asc';
        }
        if ($orderBy) {
            $builder->orderBy($orderBy, $orderByDesc);
        }
        return $builder;
    }

    static function table()
    {
        return (new static())->getTable();
    }

    static function staticQuery($key)
    {
        /** @var Builder $query */
        $query = static::query();
        if (!is_array($key)) {
            $query = $query->where('id', $key);
        } else {
            $query = $query->whereIn('id', $key);
        }
        return $query;
    }

    static function clear()
    {
        /** @var Request $request */
        $request = app('request');
        $ids     = $request->input('ids');
        return (self::staticQuery($ids))->where('status', _NEW)->delete();
    }

    static function status()
    {
        /** @var Request $request */
        $request = app('request');
        $ids     = $request->input('ids');
        $status  = $request->input('status');
        return (self::staticQuery($ids)->update(['status' => $status]));
    }

    static function used($id)
    {
        return (self::staticQuery($id))->where('status', _NEW)->update(['status' => _USED]);
    }

    static function page(Request $request = null, $whenClosure = null, $columns = ['*'])
    {
        $page     = $request->input('page') ?? 1;
        $pageSize = $request->input('pageSize') ?? 20;
        if (!is_numeric($page) || !is_numeric($pageSize) || $pageSize > 5000) {
            throw new PageParamsInvalidException();
        }
        if ($request instanceof FormRequest) {
            $query = static::filter($request->validated());
        } else {
            $query = static::query();
        }
        if ($whenClosure !== null) {
            $whenClosure($query);
        }
        $result = $query->paginate(
            $pageSize,
            $columns,
            'page',
            $page
        );
        return [
            'result' => [
                'page'      => $page,
                'pageSize'  => $pageSize,
                'pageCount' => $result->lastPage(),
                'list'      => $result->items()
            ]
        ];
    }


    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
