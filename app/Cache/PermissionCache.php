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
     * @var array $permissionBrands
     */
    protected $permissionBrands = [];

    /**
     * 保存页面名称、必选权限
     * 必选权限是例如当有一个页面增删改查, 都是基于列表开始, 那么列表就是必选权限
     * 如果列表查询有过滤条件需要接口数据, 那么过滤条件对应的接口也是必选权限
     *
     * [
     *     'admin position' => [
     *         'concatName' => 'admin_position',
     *         'groupLabel' => ['系统管理', '岗位管理'],
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
     * @var array $groupBrands
     */
    protected $groupBrands = [];

    public function __construct()
    {
        $this->client = Cache::permissionGroup();
    }

    function getGroupKeys($code = null)
    {
        $client = $this->client;
        $keys   = $client->keys('shard group *');
        if ($code) {
            $specialKeys = $client->keys("special $code group");
            $keys        = array_merge($keys, $specialKeys);
        }
        return $keys;
    }

    function getPermissionKeys($code = null)
    {
        $client = $this->client;
        $keys   = $client->keys('shard permission *');
        if ($code) {
            $specialKeys = $client->keys("special $code permission");
            $keys        = array_merge($keys, $specialKeys);
        }
        return $keys;
    }

    static function safeKeys($keys, $code = null)
    {
        foreach ($keys as $key) {
            if (
                (
                    strpos($key, 'shard permission ') !== 0 &&
                    strpos($key, "special permission $code") !== 0 &&
                    strpos($key, 'shard group ') !== 0 &&
                    strpos($key, "special group $code") !== 0
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
     *             'permissionBrands' => [
     *                 ['admin_position create', '创建岗位', ['admin_position find']],
     *                 ['admin_position update', '修改岗位', ['admin_position find']],
     *                 ['admin_position find',   '岗位详情'],
     *                 ['admin_position delete', '删除岗位'],
     *             ],
     *             'groupLabel' => ['系统管理', '岗位管理'],
     *             'must'       => [
     *                 [
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
    function permissionTable($code = null)
    {
        $client = $this->client;
        $code   = "permission table $code";

        $tableData = $client->get($code) ?? [];
        if (!config('app.debug') && $tableData && count($tableData)) {
            return $tableData;
        }
        $keys        = $this->getGroupKeys($code);
        $groupBrands = $client->mget($keys);
        foreach ($groupBrands as $groupBrand) {
            $row               = ['permissionBrands' => []];
            $row['concatName'] = $groupBrand['concatName'];
            $row['groupLabel'] = $groupBrand['groupLabel'];
            $row['code']       = $groupBrand['code'];
            $must              = [];
            foreach ($groupBrand['must'] as $mustName => $mustLabel) {
                if ($mustLabel) {
                    $must[$row['concatName'] . ' ' . $mustName] = [
                        $row['concatName'] . ' ' . $mustName,
                        $mustLabel
                    ];
                }
            }
            $row['must']                   = $must;
            $tableData[$row['concatName']] = $row;
        }
        $keys             = $this->getPermissionKeys($code);
        $permissionBrands = $client->mget($keys);
        foreach ($permissionBrands as $permissionBrand) {
            $depends = [];
            $row     = $tableData[$permissionBrand['concatName']];
            $must    = $row['must'];
            if (!$permissionBrand['label'] || isset($must[$permissionBrand['name']])) {
                continue;
            }
            foreach ($permissionBrand['depends'] as $depend) {
                $depends[] = $permissionBrand['concatName'] . ' ' . $depend;
            }
            $tableData[$permissionBrand['concatName']]['permissionBrands'][$permissionBrand['name']] = [
                'name'    => $permissionBrand['name'],
                'label'   => $permissionBrand['label'],
                'depends' => $depends
            ];
        }
        $client->set($code, $tableData);
        return $tableData;
    }

    function getGroupBrandList($guessName, $code = null)
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
                $specialKeys = $client->keys("special $code group *$guessName*");
            } else {
                $specialKeys = $client->keys("special $code group *");
            }
            $keys = array_merge($shardKeys, $specialKeys);
        }
        $data = $client->mget($keys);
        if (count($data) !== count($keys)) {
            throw new BurstException(BurstException::class, 'pc-ggb');
        }
        $result = [];
        for ($i = 0; $i < count($data); $i++) {
            $result[] = [
                $data[$i]['concatName'],
                json_encode($data[$i]['groupLabel'], JSON_UNESCAPED_UNICODE),
                json_encode($data[$i]['must'], JSON_UNESCAPED_UNICODE)
            ];
        }
        return $result;
    }

    function getPermissionBrandList($guessName, $code = null)
    {
        $client = $this->client;
        if ($guessName) {
            $shardKeys = $client->keys("shard permission *$guessName*");
        } else {
            $shardKeys = $client->keys('shard permission *');
        }
        $keys = $shardKeys;
        if ($code !== null) {
            if ($guessName) {
                $specialKeys = $client->keys("special $code permission *$guessName*");
            } else {
                $specialKeys = $client->keys("special $code permission *");
            }
            $keys = array_merge($shardKeys, $specialKeys);
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
            $this->loopPermissionGroups(require $pathname);
            $shard = strpos($filename, '_') === 0;
            $code  = null;
            if (!$shard) {
                $special = preg_match('@^[a-zA-Z0-9-]@', $filename, $matched);
                if ($special) {
                    $code = $matched[0];
                    $client->setOption(2, 'special ' . $code . ' ');
                } else {
                    throw new BurstException('不是约定的文件命名');
                }
            } else {
                $prefix = $client->setOption(2, 'shard ');
            }
            foreach ($this->permissionBrands as $ownName => $brand) {
                $brand['code'] = $code;
                $client->set('permission ' . $ownName, $brand);
            }
            foreach ($this->groupBrands as $groupName => $brand) {
                $brand['code'] = $code;
                $client->set('group ' . $groupName, $brand);
            }
        }
    }

    private function loopPermissionGroups($permissionGroups, $options = [])
    {
        foreach ($permissionGroups as $permissionGroup) {
            $name       = $permissionGroup['name'] ?? '';
            $label      = $permissionGroup['label'] ?? null;
            $actions    = $permissionGroup['actions'] ?? [];
            $children   = $permissionGroup['children'] ?? [];
            $groupLabel = $options['groupLabel'] ?? [];
            if ($label) {
                $groupLabel[] = $label;
            }
            $concatName = $options['concatName'] ?? '';
            if ($concatName) {
                $concatName .= ' ' . $name;
            } else {
                $concatName = $name;
            }
            if (count($children)) {
                $this->loopPermissionGroups($children, [
                    'groupLabel' => $groupLabel,
                    'concatName' => $concatName,
                ]);
                return;
            }
            if (count($actions)) {
                foreach ($actions as $actionName => $actionInfo) {
                    $depends = array_slice($actionInfo, 2);
                    if (!isset($this->groupBrands[$concatName])) {
                        $this->groupBrands[$concatName] = [
                            'groupLabel' => $groupLabel,
                            'concatName' => $concatName,
                            'must'       => []
                        ];
                    }
                    $permissionName = $concatName . ' ' . $actionName;
                    $must           = $actionInfo[0];
                    if ($must === 1) {
                        $this->groupBrands[$concatName]['must'][$actionName] = $actionInfo[1] ?? null;
                    }
                    $this->permissionBrands[$permissionName] = [
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
