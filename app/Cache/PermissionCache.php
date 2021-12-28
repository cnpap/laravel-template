<?php

namespace App\Cache;

use App\Exceptions\BurstException;
use Predis\Client;

class PermissionCache
{
    const P_DASHBOARD        = 'dashboard';
    const P_SYSTEM           = 'system';
    const P_ADMIN_USER       = 'admin_user';
    const P_ADMIN_ROLE       = 'admin_role';
    const P_ADMIN_POSITION   = 'admin_position';
    const P_ADMIN_DEPARTMENT = 'admin_department';

    /**
     * pRedis 客户端
     *
     * @var Client
     */
    protected $client;

    /**
     * 保存权限分组信息
     *
     * [
     *     'admin position create' => [
     *         'depends'    => [
     *             'find'
     *         ],
     *         'name'       => 'admin_position create',
     *         'concatName' => 'admin_position',
     *         'label'      => '创建岗位',
     *         // 商户 code
     *         'code'       => null
     *     ]
     * ]
     *
     * @var array $items
     */
    protected $items = [];

    /**
     * 保存页面名称、必选权限
     * 必选权限是例如当有一个页面增删改查, 都是基于列表开始, 那么列表就是必选权限
     * 如果列表查询有过滤条件需要接口数据, 那么过滤条件对应的接口也是必选权限
     *
     * [
     *     'admin position' => [
     *         'concatName' => 'admin_position',
     *         'labels'     => ['系统管理', '岗位管理'],
     *         'must'       => [
     *             'list'                     => '查看岗位列表',
     *             'admin_department_options' => null
     *         ]
     *         // 商户 code
     *         'code'       => null
     *     ]
     * ]
     *
     * admin_department_options 为 null 是因为, 作为过滤条件, 主观的认为用户没必要知道
     *
     * @var array $group
     */
    protected $group = [];

    public function __construct()
    {
        $this->client = Cache::permissionGroup();
    }

    function getGroupKeys($code = null)
    {
        $client = $this->client;
        $keys   = $client->keys('shard group *');
        if ($code) {
            $privateKeys = $client->keys("private $code group");
            $keys        = array_merge($keys, $privateKeys);
        }
        return $keys;
    }

    function getItemKeys($code = null)
    {
        $client = $this->client;
        $keys   = $client->keys('shard item *');
        if ($code) {
            $privateKeys = $client->keys("private $code item *");
            $keys        = array_merge($keys, $privateKeys);
        }
        return $keys;
    }

    function getItemNames($code = null)
    {
        $keys  = $this->getItemKeys($code);
        $names = [];
        foreach ($keys as $key) {
            preg_match('@(shard|private [a-z_]+) item ([a-z_]+) ([a-z_]+)@', $key, $matched);
            if (!isset($names[$matched[2]])) {
                $names[$matched[2]] = [];
            }
            $names[$matched[2]][] = $matched[3];
        }
        return $names;
    }

    static function safeKeys($keys, $code = null)
    {
        foreach ($keys as $key) {
            if (
                (
                    strpos($key, 'shard item ') !== 0 &&
                    strpos($key, "private item $code") !== 0 &&
                    strpos($key, 'shard group ') !== 0 &&
                    strpos($key, "private group $code") !== 0
                ) ||
                preg_match('@[^a-z0-9 ]@', $key)
            ) {
                throw new BurstException(BurstException::DANGER, 'pc-sk');
            }
        }
        return true;
    }

    /**
     *
     * [
     *     'admin_position' => [
     *         [
     *             'code'       => null
     *             'concatName' => 'admin_position',
     *             'snapshot'   => [
     *                 ['admin_position create', '创建岗位', ['admin_position find']],
     *                 ['admin_position update', '修改岗位', ['admin_position find']],
     *                 ['admin_position find',   '岗位详情'],
     *                 ['admin_position delete', '删除岗位'],
     *             ],
     *             'labels'     => ['系统管理', '岗位管理'],
     *             'must'       => [
     *                 "admin_position list" => [
     *                     'admin_position list',
     *                     '查看岗位列表'
     *                 ]
     *             ]
     *         }
     *     ]
     * ]
     *
     * @return array|false|mixed|string|void
     */
    function itemTable($code = null)
    {
        $client = $this->client;
        $code   = "item table $code";

        $tableData = $client->get($code) ?? [];
        if (!config('app.debug') && $tableData && count($tableData)) {
            return $tableData;
        }
        $keys  = $this->getGroupKeys($code);
        $group = $client->mget($keys);
        foreach ($group as $item) {
            $row               = ['snapshot' => []];
            $row['concatName'] = $item['concatName'];
            $row['labels']     = $item['labels'];
            $row['code']       = $item['code'];
            $must              = [];
            foreach ($item['must'] as $mustName => $mustLabel) {
                if ($mustLabel) {
                    $name        = $row['concatName'] . ' ' . $mustName;
                    $must[$name] = [
                        $name,
                        $mustLabel
                    ];
                }
            }
            $row['must']                   = $must;
            $tableData[$row['concatName']] = $row;
        }
        $keys  = $this->getItemKeys($code);
        $items = $client->mget($keys);
        foreach ($items as $item) {
            $depends = [];
            $row     = $tableData[$item['concatName']];
            $must    = $row['must'];
            if (!$item['label'] || isset($must[$item['name']])) {
                continue;
            }
            foreach ($item['depends'] as $depend) {
                $depends[] = $item['concatName'] . ' ' . $depend;
            }
            $tableData[$item['concatName']]['snapshot'][$item['name']] = [
                'name'    => $item['name'],
                'label'   => $item['label'],
                'depends' => $depends
            ];
        }
        $client->set($code, $tableData);
        return $tableData;
    }

    function getGroupList($guessName, $code = null)
    {
        $client = $this->client;
        if ($guessName) {
            $shardKeys = $client->keys("shard group *$guessName*");
        } else {
            $shardKeys = $client->keys('shard group *');
        }
        $keys = $shardKeys;
        if ($code !== null) {
            if ($guessName) {
                $privateKeys = $client->keys("private $code group *$guessName*");
            } else {
                $privateKeys = $client->keys("private $code group *");
            }
            $keys = array_merge($shardKeys, $privateKeys);
        }
        $data = $client->mget($keys);
        if (count($data) !== count($keys)) {
            throw new BurstException(BurstException::class, 'pc-ggb');
        }
        $result = [];
        for ($i = 0; $i < count($data); $i++) {
            $result[] = [
                $data[$i]['concatName'],
                json_encode($data[$i]['labels'], JSON_UNESCAPED_UNICODE),
                json_encode($data[$i]['must'], JSON_UNESCAPED_UNICODE)
            ];
        }
        return $result;
    }

    function getItemList($guessName, $code = null)
    {
        $client = $this->client;
        if ($guessName) {
            $shardKeys = $client->keys("shard item *$guessName*");
        } else {
            $shardKeys = $client->keys('shard item *');
        }
        $keys = $shardKeys;
        if ($code !== null) {
            if ($guessName) {
                $privateKeys = $client->keys("private $code item *$guessName*");
            } else {
                $privateKeys = $client->keys("private $code item *");
            }
            $keys = array_merge($shardKeys, $privateKeys);
        }
        $data = $client->mget($keys);
        if (is_bool($data) || count($data) !== count($keys)) {
            throw new BurstException(BurstException::INVALID_DATA, 'pc2');
        }
        $result = [];
        for ($i = 0; $i < count($data); $i++) {
            $result[] = [
                $data[$i]['name'],
                $data[$i]['concatName'],
                $data[$i]['label'],
                json_encode($data[$i]['depends'])
            ];
        }
        return $result;
    }

    function flush()
    {
        $this->client->flushDB();
    }

    function migrate()
    {
        $resourcePath = resource_path('permissions');
        $filenames    = scandir($resourcePath);
        $filenames    = array_slice($filenames, 2);
        $this->flush();
        $client = $this->client;
        foreach ($filenames as $filename) {
            $pathname = $resourcePath . '/' . $filename;
            $this->loopGroup(require $pathname);
            $shard = strpos($filename, '_') === 0;
            $code  = null;
            if (!$shard) {
                $private = preg_match('@^[a-zA-Z0-9-]@', $filename, $matched);
                if ($private) {
                    $code = $matched[0];
                    $client->setOption(2, 'private ' . $code . ' ');
                } else {
                    throw new BurstException('不是约定的文件命名');
                }
            } else {
                $prefix = $client->setOption(2, 'shard ');
            }
            foreach ($this->items as $ownName => $item) {
                $item['code'] = $code;
                $client->set('item ' . $ownName, $item);
            }
            foreach ($this->group as $groupName => $item) {
                $item['code'] = $code;
                $client->set('group ' . $groupName, $item);
            }
        }
    }

    private function loopGroup($permissionGroups, $options = [])
    {
        foreach ($permissionGroups as $permissionGroup) {
            $name     = $permissionGroup['name'] ?? '';
            $label    = $permissionGroup['label'] ?? null;
            $actions  = $permissionGroup['actions'] ?? [];
            $children = $permissionGroup['children'] ?? [];
            $labels   = $options['labels'] ?? [];
            if ($label) {
                $labels[] = $label;
            }
            $concatName = $options['concatName'] ?? '';
            if ($concatName) {
                $concatName .= ' ' . $name;
            } else {
                $concatName = $name;
            }
            if (count($children)) {
                $this->loopGroup($children, [
                    'labels'     => $labels,
                    'concatName' => $concatName,
                ]);
                return;
            }
            if (count($actions)) {
                foreach ($actions as $actionName => $actionInfo) {
                    $depends = array_slice($actionInfo, 2);
                    if (!isset($this->group[$concatName])) {
                        $this->group[$concatName] = [
                            'labels'     => $labels,
                            'concatName' => $concatName,
                            'must'       => []
                        ];
                    }
                    $permissionName = $concatName . ' ' . $actionName;
                    $must           = $actionInfo[0];
                    if ($must === 1) {
                        $this->group[$concatName]['must'][$actionName] = $actionInfo[1] ?? null;
                    }
                    $this->items[$permissionName] = [
                        'name'       => $permissionName,
                        'concatName' => $concatName,
                        'label'      => $actionInfo[1] ?? null,
                        'depends'    => $depends
                    ];
                }
            }
        }
    }
}
