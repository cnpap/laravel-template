<?php

namespace App\Cache;

use App\Exceptions\BurstException;
use Predis\Client;

class PermissionCache
{
    const PDashboard  = 'dashboard';
    const PSystem     = 'system';
    const PEnterprise = 'enterprise';

    const PAdmin           = 'admin';
    const PAdminUser       = 'adminUser';
    const PAdminRole       = 'adminRole';
    const PAdminPosition   = 'adminPosition';
    const PAdminDepartment = 'adminDepartment';

    const PDev         = 'dev';
    const PDevCategory = 'devCategory';

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
     *     'adminPosition create' => [
     *         'depends'    => [
     *             'find'
     *         ],
     *         'name'       => 'adminPosition create',
     *         'concatName' => 'adminPosition',
     *         'label'      => '创建岗位',
     *         // 租户 code
     *         'code'       => null,
     *         'must'       => 2
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
     *     'adminPosition' => [
     *         'concatName' => 'adminPosition',
     *         'labels'     => ['系统管理', '岗位管理'],
     *         'must'       => [
     *             'list'                   => '查看岗位列表',
     *             'AdminDepartmentOptions' => null
     *         ],
     *         // 租户 code
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
            $privateKeys = $client->keys("private $code GROUP");
            $keys        = array_merge($keys, $privateKeys);
        }
        return $keys;
    }

    function getGroupNames($code = null)
    {
        $keys   = $this->getGroupKeys($code);
        $pages  = [];
        $client = $this->client;
        foreach ($keys as $key) {
            $group              = $client->get($key);
            $page               = [];
            $page['labels']     = $group['labels'];
            $page['concatName'] = $group['concatName'];
            $pages[]            = $page;
        }
        return $pages;
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

    function getAuthInfo($code = null, $keys = null)
    {
        if ($keys === null) {
            $keys = $this->getItemKeys($code);
        }
        $menus  = [];
        $names  = [];
        $client = $this->client;
        foreach ($keys as $key) {
            preg_match('@(shard|private [a-z_]+) item (([a-zA-Z0-9_]+ )+)([a-zA-Z0-9_]+)@', $key, $matched);
            $type       = $matched[1];
            $pageName   = $matched[3];
            $concatName = $matched[2];
            $concatName = substr($concatName, 0, -1);
            $method     = $matched[4];

            /**
             * 记录每个页面对应的权限
             */
            if (!isset($names[$pageName])) {
                $names[$pageName] = [];
            }
            $names[$pageName][] = $method;

            /**
             * 生成后台当中的侧边菜单
             * 拆分 page 为多个 module, module 用以生成树形结构菜单
             * 每个 module 将第一个对应前端一个位于 src/PageDash 文件目录
             */
            $groupKey = sprintf("%s group %s", $type, $concatName);
            $group    = $client->get($groupKey);
            // 生成侧边栏数据
            $modules     = $group['modules'];
            $moduleProxy = null;
            foreach ($modules as $name => $label) {
                $module = strtoupper($name[0]) . substr($name, 1);
                if ($moduleProxy === null) {
                    if (!isset($menus[$module])) {
                        $menus[$module] = [
                            'label'    => $label,
                            'module'   => $module,
                            'children' => [],
                        ];
                    }
                    $moduleProxy = &$menus[$module];
                } else {
                    if (!isset($moduleProxy['children'][$module])) {
                        $moduleProxy['children'][$module] = [
                            'label'    => $label,
                            'module'   => $module,
                            'children' => [],
                        ];
                    }
                    $moduleProxy = &$moduleProxy['children'][$module];
                }
            }
            unset($moduleProxy);
        }
        return compact('names', 'menus');
    }

    function getPages($names, $isShard)
    {

    }

    static function safeKeys($keys, $code = null)
    {
        foreach ($keys as $key) {
            if (
                (
                    strpos($key, 'shard item ') !== 0 &&
                    strpos($key, "private $code item ") !== 0
                )
            ) {
                throw new BurstException(BurstException::DANGER, 'pc-sk');
            }
        }
        return true;
    }

    /**
     *
     * [
     *     'adminPosition' => [
     *         [
     *             'code'       => null
     *             'concatName' => 'adminPosition',
     *             'snapshot'   => [
     *                 ['adminPosition create', '创建岗位', ['find']],
     *                 ['adminPosition update', '修改岗位', ['find']],
     *                 ['adminPosition find',   '岗位详情'],
     *                 ['adminPosition delete', '删除岗位'],
     *             ],
     *             'labels'     => ['系统管理', '岗位管理'],
     *             'must'       => [
     *                 "adminPosition list" => [
     *                     'adminPosition list',
     *                     '查看岗位列表'
     *                 ]
     *             ]
     *         ]
     *     ]
     * ]
     *
     * @return array|false|mixed|string|void
     */
    function itemTable($checkedNames, $code = null)
    {
        $client    = $this->client;
        $code      = "item table $code";
        $tableData = $client->get($code) ?? [];
        $traces    = [];
        if (!config('app.debug') && $tableData && count($tableData)) {
            return $tableData;
        }
        $keys  = $this->getGroupKeys($code);
        $group = $client->mget($keys);
        foreach ($group as $item) {
            $row               = ['snapshot' => []];
            $row['concatName'] = $item['concatName'];
            $row['code']       = $item['code'];
            $row['labels']     = array_values($item['modules']);
            $must              = [];
            foreach ($item['must'] as $mustName => $mustLabel) {
                if ($mustLabel) {
                    $name = $row['concatName'] . ' ' . $mustName;
                    if ($row['code'] === null) {
                        $name = "shard item $name";
                    } else {
                        $name = "private $code item $name";
                    }
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
            if ($item['must'] === 1) {
                continue;
            }
            $depends = [];
            $row     = $tableData[$item['concatName']];
            $code    = $row['code'];
            $must    = $row['must'];
            if (isset($must[$item['name']])) {
                continue;
            }
            if ($code === null) {
                $prefix = "shard item ";
            } else {
                $prefix = "private $code item ";
            }
            foreach ($item['depends'] as $depend) {
                $depends[] = $prefix . $item['concatName'] . ' ' . $depend;
            }
            $currName = $prefix . $item['name'];
            if (in_array($currName, $checkedNames)) {
                if (!isset($traces[$item['concatName']])) {
                    $traces[$item['concatName']] = [];
                }
                $traces[$item['concatName']][] = $currName;
            }
            $tableData[$item['concatName']]['snapshot'][$prefix . $item['name']] = [
                'name'    => $prefix . $item['name'],
                'label'   => $item['label'] ?? null,
                'depends' => $depends
            ];
        }
        $client->set($code, $tableData);
        return compact('tableData', 'traces');
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
                json_encode($data[$i]['modules'], JSON_UNESCAPED_UNICODE),
                json_encode($data[$i]['must'], JSON_UNESCAPED_UNICODE)
            ];
        }
        return collect($result)->sortBy(0)->all();
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
        return collect($result)->sortBy(1)->all();
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
            $shard    = strpos($filename, '_') === 0;
            $code     = null;
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
            $this->loopGroup(require $pathname, ['code' => $code]);
            foreach ($this->items as $ownName => $item) {
                $client->set('item ' . $ownName, $item);
            }
            foreach ($this->group as $groupName => $item) {
                $client->set('group ' . $groupName, $item);
            }
        }
    }

    private function loopGroup($permissionGroups, $options = [])
    {
        $code = $options['code'];
        foreach ($permissionGroups as $permissionGroup) {
            $label    = $permissionGroup['label'] ?? null;
            $actions  = $permissionGroup['actions'] ?? [];
            $children = $permissionGroup['children'] ?? [];
            $name     = $permissionGroup['name'] ?? null;
            $modules  = $options['modules'] ?? [];
            if ($label) {
                $modules[$name] = $label;
            }
            $concatName = $options['concatName'] ?? '';
            if ($concatName) {
                $concatName .= ' ' . $name;
            } else {
                $concatName = $name;
            }
            if (count($children)) {
                $this->loopGroup($children, [
                    'code'       => $code,
                    'modules'    => $modules,
                    'concatName' => $concatName,
                ]);
                continue;
            }
            if (count($actions)) {
                foreach ($actions as $actionName => $actionInfo) {
                    $depends = array_slice($actionInfo, 2);
                    if (!isset($this->group[$concatName])) {
                        $this->group[$concatName] = [
                            'modules'    => $modules,
                            'concatName' => $concatName,
                            'must'       => [],
                            'code'       => $code,
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
                        'depends'    => $depends,
                        'must'       => $must
                    ];
                }
            }
        }
    }
}
