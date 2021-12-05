<?php

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Overtrue\LaravelPinyin\Facades\Pinyin;
use Illuminate\Http\Request;
use http\Exception\RuntimeException;

// OK 也代表数据是新增数据
const _NEW = '新数据';
const _ERR = '异常中';
const _OFF = '已停用';

// USED 有过关联的数据, 一般是不可以被删除的
const _USED = '已使用';

const _STOP = '已下架';

define("STATUS_JOIN", implode(',', [_NEW, _OFF, _USED, _ERR]));

const _MAN   = '男';
const _WOMAN = '女';

define("GENDER_JOIN", implode(',', [_MAN, _WOMAN]));

function lockMiddleware($name)
{
    return function (Request $request, $next) use ($name) {
        sess($name);
        return $next($request);
    };
}

function mergeCode(&$post, $field = 'name', $codeField = 'code')
{
    $code = $post[$codeField] ?? null;
    if (!$code) {
        $post[$codeField] = $post[$field];
    }
}

function rsaDecrypt($data)
{
    $prvKey = file_get_contents(storage_path('app/prv'));
    $data   = base64_decode($data);
    openssl_private_decrypt($data, $text, $prvKey);
    return $text;
}

function fnPinYin($data)
{
    return Pinyin::abbr($data, PINYIN_KEEP_NUMBER | PINYIN_KEEP_ENGLISH | PINYIN_KEEP_PUNCTUATION);
}

function sess($name)
{
    $eid = Session::get($name);
    if (!$eid) {
        throw new RuntimeException();
    }
    return $eid;
}

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

function uni()
{
    return uniqid();
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
    $data['message'] = $data['message'] ?? '请求失败请重试';
    return response()->json($data, $data['code']);
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

function upload($options)
{
    /** @var Request $request */
    $request = app('request');
    $file    = $request->file('file');
    $ext     = $file->extension();
    $extType = $options['extType'];
    switch ($extType) {
        case 'image':
            if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                throw new RuntimeException();
            }
            break;
        case 'video':
            if (!in_array($ext, ['mp4', ''])) {
                throw new RuntimeException();
            }
            break;
        case 'excel':
            if (!in_array($ext, ['csv', 'xlsx'])) {
                throw new RuntimeException();
            }
            break;
        default:
            throw new RuntimeException();
    }
    $eid      = sess('eid');
    $path     = $options['path'];
    $fileId   = uniqid();
    $filename = $file->getClientOriginalName();
    $filename = "$eid-$fileId-$filename";
    $filePath = "$path/$filename";
    $file->move(public_path($path), $filename);
    return $filePath;
}

function resultImg($imgUrl)
{
    return result(['img_url' => $imgUrl]);
}

function usePage()
{
    /** @var Request $request */
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

function options($item, $key = 'name')
{
    if ($item instanceof Collection) {
        $item = $item->toArray();
    }
    $result = [];
    foreach ($item as $row) {
        $result[] = [
            'label' => $row[$key],
            'key'   => $row['id'],
            'value' => $row['id'],
        ];
    }
    return $result;
}

function treeOptions($item)
{
    if ($item instanceof Collection) {
        $item = $item->toArray();
    }
    $result = [];
    for ($i = 0; $i < count($item); $i++) {
        if (!$item[$i]['pid']) {
            $current  = array_splice($item, $i--, 1)[0];
            $children = treeTn($item, $current['id']);
            $model    = [
                'label'  => $current['name'],
                'value'  => $current['id'],
                'key'    => $current['id'],
                'isLeaf' => true
            ];
            if (count($children)) {
                $model['children'] = $children;
                $model['isLeaf']   = false;
            }
            $result[] = array_merge($current, $model);
        }
    }
    return $result;
}

function treeTn($item, $id = '')
{
    $tn = [];
    for ($i = 0; $i < count($item); $i++) {
        if ($item[$i]['pid'] == $id) {
            $current  = array_splice($item, $i--, 1)[0];
            $children = treeTn($item, $current['id']);
            $model    = [
                'label'  => $current['name'],
                'value'  => $current['id'],
                'key'    => $current['id'],
                'isLeaf' => true
            ];
            if (count($children)) {
                $model['children'] = $children;
                $model['isLeaf']   = false;
            }
            $tn[] = array_merge($model, $current);
        }
    }
    return $tn;
}
