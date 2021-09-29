<?php

use App\Exceptions\PageParamsInvalidException;
use Godruoyi\Snowflake\Snowflake;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Overtrue\LaravelPinyin\Facades\Pinyin;

// OK 也代表数据是新增数据
const OK  = 1;
const ERR = 2;

// USED 有过关联的数据, 一般是不可以被删除的
const USED = 99;

/**
 * @method static array convert(string $data)
 */
class PYin extends Pinyin
{
    static function simple(string $character)
    {
        $runeItem = self::convert($character);
        $value    = '';
        foreach ($runeItem as $runes) {
            $value .= $runes[0];
        }
        return $value;
    }

    static function array(array $arr)
    {
        $item = [];
        foreach ($arr as $character) {
            $item[] = self::simple($character);
        }
        return implode(', ', $item);
    }
}

class Uni
{
    static $lastTime;
    static $sequence;
}

function uni()
{
    $snowflake = new Snowflake();
    return $snowflake->setSequenceResolver(function ($currentTime) {
        if (Uni::$lastTime == $currentTime) {
            ++Uni::$sequence;
        } else {
            Uni::$sequence = 0;
        }

        Uni::$lastTime = $currentTime;

        return Uni::$sequence;
    })->id();
}

// status success
function ss($data = [])
{
    $data['code'] = 200;
    return response($data);
}

// status error
function se($data = [])
{
    $data['code']    = $data['code'] ?? 500;
    $data['message'] = '请求失败请重试';
    return response($data, $data['code']);
}

// transaction
function tx($ok, $onOk = null, $onFail = null)
{
    if ($ok) {
        if ($onOk) {
            $onOk();
        }
        return ss();
    }
    if ($onFail) {
        $onFail();
    }
    return se();
}

function result($data)
{
    return ss(['result' => $data]);
}

function usePage()
{
    /** @var \Illuminate\Http\Request $request */
    $request  = app('request');
    $page     = $request->input('page', 0);
    $pageSize = $request->input('pageSize', 0);
    $columns  = ['*'];
    return [$pageSize, $columns, 'page', $page];
}

function page(LengthAwarePaginator $paginator, $result = [])
{
    $result['pageCount'] = $paginator->lastPage();
    $result['list']      = $paginator->items();
    return result($result);
}