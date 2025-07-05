<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 清除快取
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 建立基本權限
        $permissions = [
            'users.view' => '查看用戶',
            'users.create' => '建立用戶',
            'users.edit' => '編輯用戶',
            'users.delete' => '刪除用戶',
            'users.assign_roles' => '分配用戶角色',
            'organizations.view' => '查看組織',
            'organizations.create' => '建立組織',
            'organizations.edit' => '編輯組織',
            'organizations.delete' => '刪除組織',
            'organizations.manage_members' => '管理組織成員',
            'roles.view' => '查看角色',
            'roles.create' => '建立角色',
            'roles.edit' => '編輯角色',
            'roles.delete' => '刪除角色',
            'roles.assign_permissions' => '分配角色權限',
            'permissions.view' => '查看權限',
            'permissions.manage' => '管理權限',
        ];

        foreach ($permissions as $permission => $description) {
            Permission::firstOrCreate([
                'name' => $permission,
                'description' => $description,
                'guard_name' => 'api'
            ]);
        }

        // 建立基本角色
        $roles = [
            'super_admin' => [
                'name' => '超級管理員',
                'description' => '擁有系統所有權限',
                'permissions' => array_keys($permissions)
            ],
            'admin' => [
                'name' => '管理員',
                'description' => '擁有大部分管理權限',
                'permissions' => [
                    'users.view',
                    'users.create',
                    'users.edit',
                    'users.delete',
                    'users.assign_roles',
                    'organizations.view',
                    'organizations.create',
                    'organizations.edit',
                    'organizations.delete',
                    'organizations.manage_members',
                    'roles.view',
                    'roles.create',
                    'roles.edit',
                    'roles.delete',
                    'roles.assign_permissions',
                    'permissions.view',
                    'permissions.manage',
                ]
            ],
            'manager' => [
                'name' => '主管',
                'description' => '擁有部門管理權限',
                'permissions' => [
                    'users.view',
                    'users.create',
                    'users.edit',
                    'organizations.view',
                    'organizations.edit',
                    'organizations.manage_members',
                    'roles.view',
                    'permissions.view',
                ]
            ],
            'employee' => [
                'name' => '員工',
                'description' => '基本查看權限',
                'permissions' => [
                    'users.view',
                    'organizations.view',
                    'roles.view',
                ]
            ],
            'viewer' => [
                'name' => '檢視者',
                'description' => '僅有查看權限',
                'permissions' => [
                    'users.view',
                    'organizations.view',
                ]
            ],
        ];

        foreach ($roles as $roleKey => $roleData) {
            $role = Role::firstOrCreate([
                'name' => $roleKey,
                'description' => $roleData['description'],
                'guard_name' => 'api'
            ]);
            $role->syncPermissions($roleData['permissions']);
        }
    }
}
