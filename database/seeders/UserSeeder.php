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
        User::create([
            'name' => '超級管理員',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'phone' => '0912345678',
            'employee_id' => 'ADMIN001',
            'is_active' => true,
        ]);

        // 建立一般用戶
        $users = [
            [
                'name' => '張小明',
                'email' => 'zhang.xiaoming@example.com',
                'phone' => '0923456789',
                'employee_id' => 'EMP001',
            ],
            [
                'name' => '李小華',
                'email' => 'li.xiaohua@example.com',
                'phone' => '0934567890',
                'employee_id' => 'EMP002',
            ],
            [
                'name' => '王小美',
                'email' => 'wang.xiaomei@example.com',
                'phone' => '0945678901',
                'employee_id' => 'EMP003',
            ],
            [
                'name' => '陳大強',
                'email' => 'chen.daqiang@example.com',
                'phone' => '0956789012',
                'employee_id' => 'EMP004',
            ],
            [
                'name' => '林小芳',
                'email' => 'lin.xiaofang@example.com',
                'phone' => '0967890123',
                'employee_id' => 'EMP005',
            ],
            [
                'name' => '黃志明',
                'email' => 'huang.zhiming@example.com',
                'phone' => '0978901234',
                'employee_id' => 'EMP006',
            ],
            [
                'name' => '劉雅婷',
                'email' => 'liu.yating@example.com',
                'phone' => '0989012345',
                'employee_id' => 'EMP007',
            ],
            [
                'name' => '吳建志',
                'email' => 'wu.jianzhi@example.com',
                'phone' => '0990123456',
                'employee_id' => 'EMP008',
            ],
            [
                'name' => '許淑芬',
                'email' => 'xu.shufen@example.com',
                'phone' => '0901234567',
                'employee_id' => 'EMP009',
            ],
            [
                'name' => '蔡明德',
                'email' => 'cai.mingde@example.com',
                'phone' => '0911111111',
                'employee_id' => 'EMP010',
            ],
        ];

        foreach ($users as $userData) {
            User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'phone' => $userData['phone'],
                'employee_id' => $userData['employee_id'],
                'is_active' => true,
            ]);
        }

        // 建立一些未驗證的用戶（用於測試）
        User::create([
            'name' => '未驗證用戶',
            'email' => 'unverified@example.com',
            'email_verified_at' => null,
            'password' => Hash::make('password123'),
            'phone' => '0922222222',
            'employee_id' => 'EMP011',
            'is_active' => true,
        ]);

        // 建立一個停用的用戶（用於測試）
        User::create([
            'name' => '停用用戶',
            'email' => 'disabled@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'phone' => '0933333333',
            'employee_id' => 'EMP012',
            'is_active' => false,
        ]);
    }
}
