<?php

use App\Cache\PermissionCache;

return [
    [
        'label'   => '仪表盘',
        'name'    => PermissionCache::P_DASHBOARD,
        'actions' => []
    ],
    [
        'label'   => '系统设置',
        'name'    => PermissionCache::P_SYSTEM,
        'actions' => []
    ],
    [
        'label'    => '人员管理',
        'children' => [
            [
                'label'   => '用户管理',
                'name'    => PermissionCache::P_ADMIN_USER,
                'actions' => [
                    'create'                   => [2, '创建管理员用户'],
                    'update'                   => [2, '修改管理员用户', 'find'],
                    'delete'                   => [2, '删除管理员用户'],
                    'status'                   => [2, '修改管理员用户状态'],
                    'find'                     => [2, '查看管理员用户详情', 'admin_position_options'],
                    'list'                     => [1, '查看管理员用户列表'],
                    'admin_department_options' => [1],
                    'admin_position_options'   => [2],
                    'admin_permission_options' => [2]
                ]
            ],
            [
                'label'   => '角色管理',
                'name'    => PermissionCache::P_ADMIN_ROLE,
                'actions' => [
                    'create'                => [2, '创建角色'],
                    'update'                => [2, '修改角色', 'find'],
                    'delete'                => [2, '删除角色'],
                    'status'                => [2, '修改角色状态'],
                    'find'                  => [2, '查看角色详情'],
                    'list'                  => [1, '查看角色列表'],
                    'sync_permission_names' => [2, '关联权限给角色', 'permission_table'],
                    'permission_table'      => [1],
                ]
            ],
            [
                'label'   => '岗位管理',
                'name'    => PermissionCache::P_ADMIN_POSITION,
                'actions' => [
                    'create'                   => [2, '创建岗位'],
                    'update'                   => [2, '修改岗位', 'find'],
                    'delete'                   => [2, '删除岗位'],
                    'status'                   => [2, '修改岗位状态'],
                    'find'                     => [2, '查看岗位详情'],
                    'list'                     => [1, '查看岗位列表'],
                    'admin_department_options' => [1],
                ]
            ],
            [
                'label'   => '部门管理',
                'name'    => PermissionCache::P_ADMIN_DEPARTMENT,
                'actions' => [
                    'create' => [2, '创建部门'],
                    'update' => [2, '修改部门', 'find'],
                    'delete' => [2, '删除部门'],
                    'status' => [2, '修改部门状态'],
                    'find'   => [2, '查看部门详情'],
                    'list'   => [1, '查看部门列表'],
                ]
            ],
        ]
    ]
];
