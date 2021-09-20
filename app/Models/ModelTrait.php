<?php

namespace App\Models;

use App\Exceptions\PageParamsInvalidException;
use Carbon\Carbon;
use Closure;
use DateTimeInterface;
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

    static function table()
    {
        return (new static())->getTable();
    }

    static function staticQuery($key)
    {
        /** @var Builder $query */
        $query = static::query();
        if (is_numeric($key)) {
            $query->where('id', $key);
        } else {
            $query->whereIn('id', $key);
        }
        return $query;
    }

    static function clear($key)
    {
        return (self::staticQuery($key))->where('status', '!=', USED)->delete();
    }

    static function used($key)
    {
        return (self::staticQuery($key))->where('status', OK)->update(['status' => USED]);
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