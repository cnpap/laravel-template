<?php

namespace App\Console\Commands;

use App\Cache\PermissionCache;
use App\Models\Admin\AdminUser;
use Illuminate\Console\Command;

class CachePermissionCommand extends Command
{
    protected $signature = 'cache:permissions {code?}';

    protected $description = '操作权限缓存信息';

    /** @var PermissionCache */
    protected $permissionCacheControl;

    function freshToCache()
    {
        $this->permissionCacheControl->migrate();
        $this->info('已刷新文件结构到缓存');
    }

    /**
     * resources/permissions 文件夹下所有文件
     *
     * _ 下划线开头为通用功能, 指定租户或用户规则待定
     */
    public function handle()
    {
        $code = $this->argument('code');
        $this->permissionCacheControl = new PermissionCache();
        if ($code === 'fresh') {
            $this->freshToCache();
            return;
        }

        while (true) {
            $action = $this->choice(
                '选择菜单, <ctl + c 退出>',
                [
                    '刷新文件结构到缓存',
                    '查看权限组列表',
                    '查看权限列表',
                    '获取用户权限',
                    '验证用户权限',
                ]
            );
            if ($action === '刷新文件结构到缓存') {
                $this->freshToCache();
                return;
            }
            if ($action === '查看权限组列表') {
                $likeGroupName = $this->ask('请输入搜索 权限 name [例: admin], [默认搜索全部]');
                $getGroupList  = $this->permissionCacheControl->getGroupList($likeGroupName);
                $this->table(
                    ['权限组 name', '上级组', '必要依赖权限'],
                    $getGroupList
                );
                $this->handle();
            }
            if ($action === '查看权限列表') {
                $likeName    = $this->ask('请输入搜索 权限 name [例: admin], [默认搜索全部]');
                $getItemList = $this->permissionCacheControl->getItemList($likeName);
                $this->table(
                    ['权限 name', '所在权限组', '权限名称', '依赖权限'],
                    $getItemList
                );
                $this->handle();
            }
            if ($action === '获取用户权限') {
                $mark = $this->ask('请输入搜索 用户ID/名称/编号/手机号/email');
                $mark = trim($mark);
                $many = AdminUser::filter(['detect' => $mark])
                    ->select(['id', 'username', 'code', 'phone', 'email'])
                    ->limit(10)
                    ->get();
                if ($many->count() === 0) {
                    $this->warn('没有找到用户');
                    $this->handle();
                }
                /** @var AdminUser $user */
                $user = $many[0];
                if ($user->id !== $mark) {
                    $this->table(['ID', '用户名称', '用户编号', '手机号(登陆账号)', 'email'], $many);
                    $userId = $this->ask('请回复用户ID再次确认');
                    $user   = AdminUser::filter(['id' => $userId])->first();
                    if (!$user) {
                        $this->warn('没有找到用户');
                        $this->handle();
                    }
                }
            }
        }
    }
}
