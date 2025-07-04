<?php

namespace Database\Seeders;

use App\Models\Organizations;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Enums\OrganizationStatus;

class OrganizationsSeeder extends Seeder
{
    public function run(): void
    {
        // 建立根組織
        $rootOrg = Organizations::create([
            'name' => '總公司',
            'type' => 'company',
            'parent_id' => null,
            'address' => '台北市信義區信義路五段7號',
            'phone' => '02-23456789',
            'email' => 'contact@company.com',
            'monthly_budget' => 10000000,
            'cost_center_code' => 'CC001',
            'status' => OrganizationStatus::ACTIVE,
        ]);

        // 建立部門
        $departments = [
            [
                'name' => '資訊技術部',
                'type' => 'department',
                'cost_center_code' => 'CC002',
            ],
            [
                'name' => '人力資源部',
                'type' => 'department',
                'cost_center_code' => 'CC003',
            ],
            [
                'name' => '財務部',
                'type' => 'department',
                'cost_center_code' => 'CC004',
            ],
            [
                'name' => '行銷部',
                'type' => 'department',
                'cost_center_code' => 'CC005',
            ],
        ];

        $createdDepartments = [];
        foreach ($departments as $dept) {
            $department = Organizations::create(array_merge($dept, [
                'parent_id' => $rootOrg->id,
                'address' => '台北市信義區信義路五段7號',
                'phone' => '02-23456789',
                'email' => strtolower(str_replace('部', '', $dept['name'])) . '@company.com',
                'monthly_budget' => 2000000,
                'status' => OrganizationStatus::ACTIVE,
            ]));
            $createdDepartments[] = $department;
        }

        // 將用戶分配到不同部門
        $users = User::all();
        $admin = User::where('email', 'admin@example.com')->first();

        // 管理員加入總公司
        if ($admin) {
            $admin->organizations()->attach($rootOrg->id);
        }

        // 其他用戶分配到不同部門
        $userIndex = 0;
        foreach ($users as $user) {
            if ($user->email === 'admin@example.com') continue;
            $departmentIndex = $userIndex % count($createdDepartments);
            $user->organizations()->attach($createdDepartments[$departmentIndex]->id);
            $userIndex++;
        }
    }
}
