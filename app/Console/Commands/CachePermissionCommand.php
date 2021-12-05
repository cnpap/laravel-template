<?php

namespace App\Console\Commands;

use App\Cache\PermissionCache;
use App\Models\Admin\AdminUser;
use Illuminate\Console\Command;

class CachePermissionCommand extends Command
{
    protected $signature = 'cache:permissions';

    protected $description = '操作权限缓存信息';

    /**
     * resources/permissions 文件夹下所有文件
     *
     * _ 下划线开头为通用功能, 指定租户或用户规则待定
     */
    public function handle()
    {
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
            $cache  = new PermissionCache();
            if ($action === '刷新文件结构到缓存') {
                $cache->migrate();
                $this->info('已刷新文件结构到缓存');
                $this->handle();
            }
            if ($action === '查看权限组列表') {
                $likeGroupName     = $this->ask('请输入搜索 权限 name [例: admin], [默认搜索全部]');
                $getGroupBrandList = $cache->getGroupBrandList($likeGroupName);
                $this->table(
                    ['权限组 name', '上级组', '必要依赖权限'],
                    $getGroupBrandList
                );
                $this->handle();
            }
            if ($action === '查看权限列表') {
                $likeName         = $this->ask('请输入搜索 权限 name [例: admin], [默认搜索全部]');
                $getPermissionBrandList = $cache->getPermissionBrandList($likeName);
                $this->table(
                    ['权限 name', '所在权限组', '权限名称', '依赖权限'],
                    $getPermissionBrandList
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
