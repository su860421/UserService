<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 建立超級管理員
        $admin = User::create([
            'name' => '超級管理員',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'phone' => '0912345678',
            'employee_id' => 'ADMIN001',
            'is_active' => true,
        ]);
        $admin->assignRole('super_admin');

        // 建立管理員
        $manager = User::create([
            'name' => '系統管理員',
            'email' => 'system@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'phone' => '0923456789',
            'employee_id' => 'ADMIN002',
            'is_active' => true,
        ]);
        $manager->assignRole('admin');

        // 建立主管
        $supervisor = User::create([
            'name' => '部門主管',
            'email' => 'supervisor@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'phone' => '0934567890',
            'employee_id' => 'MGR001',
            'is_active' => true,
        ]);
        $supervisor->assignRole('manager');

        // 建立一般用戶
        $users = [
            [
                'name' => '張小明',
                'email' => 'zhang.xiaoming@example.com',
                'phone' => '0923456789',
                'employee_id' => 'EMP001',
                'role' => 'employee',
            ],
            [
                'name' => '李小華',
                'email' => 'li.xiaohua@example.com',
                'phone' => '0934567890',
                'employee_id' => 'EMP002',
                'role' => 'employee',
            ],
            [
                'name' => '王小美',
                'email' => 'wang.xiaomei@example.com',
                'phone' => '0945678901',
                'employee_id' => 'EMP003',
                'role' => 'employee',
            ],
            [
                'name' => '陳大強',
                'email' => 'chen.daqiang@example.com',
                'phone' => '0956789012',
                'employee_id' => 'EMP004',
                'role' => 'employee',
            ],
            [
                'name' => '林小芳',
                'email' => 'lin.xiaofang@example.com',
                'phone' => '0967890123',
                'employee_id' => 'EMP005',
                'role' => 'employee',
            ],
            [
                'name' => '黃志明',
                'email' => 'huang.zhiming@example.com',
                'phone' => '0978901234',
                'employee_id' => 'EMP006',
                'role' => 'employee',
            ],
            [
                'name' => '劉雅婷',
                'email' => 'liu.yating@example.com',
                'phone' => '0989012345',
                'employee_id' => 'EMP007',
                'role' => 'employee',
            ],
            [
                'name' => '吳建志',
                'email' => 'wu.jianzhi@example.com',
                'phone' => '0990123456',
                'employee_id' => 'EMP008',
                'role' => 'employee',
            ],
            [
                'name' => '許淑芬',
                'email' => 'xu.shufen@example.com',
                'phone' => '0901234567',
                'employee_id' => 'EMP009',
                'role' => 'employee',
            ],
            [
                'name' => '蔡明德',
                'email' => 'cai.mingde@example.com',
                'phone' => '0911111111',
                'employee_id' => 'EMP010',
                'role' => 'employee',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::create(array_merge($userData, [
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]));

            $user->assignRole($role);
        }

        // 建立一些未驗證的用戶（用於測試）
        $unverifiedUser = User::create([
            'name' => '未驗證用戶',
            'email' => 'unverified@example.com',
            'email_verified_at' => null,
            'password' => Hash::make('password123'),
            'phone' => '0922222222',
            'employee_id' => 'EMP011',
            'is_active' => true,
        ]);
        $unverifiedUser->assignRole('viewer');

        // 建立一個停用的用戶（用於測試）
        $disabledUser = User::create([
            'name' => '停用用戶',
            'email' => 'disabled@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'phone' => '0933333333',
            'employee_id' => 'EMP012',
            'is_active' => false,
        ]);
        $disabledUser->assignRole('viewer');
    }
}
