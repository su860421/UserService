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
        // 第1層：總公司
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

        // 第2層：分公司
        $branches = [
            [
                'name' => '台北分公司',
                'type' => 'branch',
                'cost_center_code' => 'CC002',
                'address' => '台北市大安區忠孝東路四段1號',
                'phone' => '02-23456790',
                'email' => 'taipei@company.com',
            ],
            [
                'name' => '台中分公司',
                'type' => 'branch',
                'cost_center_code' => 'CC003',
                'address' => '台中市西區台灣大道二段2號',
                'phone' => '04-23456791',
                'email' => 'taichung@company.com',
            ],
            [
                'name' => '高雄分公司',
                'type' => 'branch',
                'cost_center_code' => 'CC004',
                'address' => '高雄市前金區中正路3號',
                'phone' => '07-23456792',
                'email' => 'kaohsiung@company.com',
            ],
        ];

        $createdBranches = [];
        foreach ($branches as $branch) {
            $branchOrg = Organizations::create(array_merge($branch, [
                'parent_id' => $rootOrg->id,
                'monthly_budget' => 5000000,
                'status' => OrganizationStatus::ACTIVE,
            ]));
            $createdBranches[] = $branchOrg;
        }

        // 第3層：部門
        $departments = [
            [
                'name' => '資訊技術部',
                'type' => 'department',
                'cost_center_code' => 'CC005',
            ],
            [
                'name' => '人力資源部',
                'type' => 'department',
                'cost_center_code' => 'CC006',
            ],
            [
                'name' => '財務部',
                'type' => 'department',
                'cost_center_code' => 'CC007',
            ],
            [
                'name' => '行銷部',
                'type' => 'department',
                'cost_center_code' => 'CC008',
            ],
            [
                'name' => '業務部',
                'type' => 'department',
                'cost_center_code' => 'CC009',
            ],
            [
                'name' => '研發部',
                'type' => 'department',
                'cost_center_code' => 'CC010',
            ],
        ];

        $createdDepartments = [];
        foreach ($createdBranches as $branch) {
            foreach ($departments as $dept) {
                $department = Organizations::create(array_merge($dept, [
                    'parent_id' => $branch->id,
                    'address' => $branch->address,
                    'phone' => $branch->phone,
                    'email' => strtolower(str_replace('部', '', $dept['name'])) . '@' . $branch->name . '.com',
                    'monthly_budget' => 1500000,
                    'status' => OrganizationStatus::ACTIVE,
                ]));
                $createdDepartments[] = $department;
            }
        }

        // 第4層：小組
        $teams = [
            [
                'name' => '前端開發小組',
                'type' => 'team',
                'cost_center_code' => 'CC011',
            ],
            [
                'name' => '後端開發小組',
                'type' => 'team',
                'cost_center_code' => 'CC012',
            ],
            [
                'name' => '測試小組',
                'type' => 'team',
                'cost_center_code' => 'CC013',
            ],
            [
                'name' => 'UI/UX設計小組',
                'type' => 'team',
                'cost_center_code' => 'CC014',
            ],
            [
                'name' => '招募小組',
                'type' => 'team',
                'cost_center_code' => 'CC015',
            ],
            [
                'name' => '訓練發展小組',
                'type' => 'team',
                'cost_center_code' => 'CC016',
            ],
            [
                'name' => '會計小組',
                'type' => 'team',
                'cost_center_code' => 'CC017',
            ],
            [
                'name' => '財務分析小組',
                'type' => 'team',
                'cost_center_code' => 'CC018',
            ],
            [
                'name' => '數位行銷小組',
                'type' => 'team',
                'cost_center_code' => 'CC019',
            ],
            [
                'name' => '品牌行銷小組',
                'type' => 'team',
                'cost_center_code' => 'CC020',
            ],
            [
                'name' => '國內業務小組',
                'type' => 'team',
                'cost_center_code' => 'CC021',
            ],
            [
                'name' => '國際業務小組',
                'type' => 'team',
                'cost_center_code' => 'CC022',
            ],
            [
                'name' => '產品研發小組',
                'type' => 'team',
                'cost_center_code' => 'CC023',
            ],
            [
                'name' => '技術研發小組',
                'type' => 'team',
                'cost_center_code' => 'CC024',
            ],
        ];

        $createdTeams = [];
        $teamIndex = 0;
        foreach ($createdDepartments as $department) {
            // 每個部門分配2-3個小組
            $teamsPerDept = 2;
            if (in_array($department->name, ['資訊技術部', '研發部'])) {
                $teamsPerDept = 4; // 技術部門有更多小組
            } elseif (in_array($department->name, ['人力資源部', '財務部'])) {
                $teamsPerDept = 2;
            } elseif (in_array($department->name, ['行銷部', '業務部'])) {
                $teamsPerDept = 2;
            }

            for ($i = 0; $i < $teamsPerDept; $i++) {
                if ($teamIndex < count($teams)) {
                    $team = Organizations::create(array_merge($teams[$teamIndex], [
                        'parent_id' => $department->id,
                        'address' => $department->address,
                        'phone' => $department->phone,
                        'email' => 'team' . $teamIndex . '@' . str_replace(' ', '', $department->name) . '.com',
                        'monthly_budget' => 500000,
                        'status' => OrganizationStatus::ACTIVE,
                    ]));
                    $createdTeams[] = $team;
                    $teamIndex++;
                }
            }
        }

        // 第5層：子小組
        $subTeams = [
            [
                'name' => 'React開發子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC025',
            ],
            [
                'name' => 'Vue開發子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC026',
            ],
            [
                'name' => 'Go後端子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC027',
            ],
            [
                'name' => 'PHP後端子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC028',
            ],
            [
                'name' => '自動化測試子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC029',
            ],
            [
                'name' => '手動測試子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC030',
            ],
            [
                'name' => '視覺設計子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC031',
            ],
            [
                'name' => '互動設計子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC032',
            ],
            [
                'name' => '社會招募子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC033',
            ],
            [
                'name' => '校園招募子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC034',
            ],
            [
                'name' => '新進員工訓練子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC035',
            ],
            [
                'name' => '在職訓練子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC036',
            ],
            [
                'name' => '應收帳款子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC037',
            ],
            [
                'name' => '應付帳款子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC038',
            ],
            [
                'name' => '預算規劃子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC039',
            ],
            [
                'name' => '投資分析子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC040',
            ],
            [
                'name' => '社群媒體子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC041',
            ],
            [
                'name' => '內容行銷子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC042',
            ],
            [
                'name' => '廣告投放子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC043',
            ],
            [
                'name' => '活動行銷子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC044',
            ],
            [
                'name' => '北區業務子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC045',
            ],
            [
                'name' => '中區業務子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC046',
            ],
            [
                'name' => '南區業務子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC047',
            ],
            [
                'name' => '東南亞業務子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC048',
            ],
            [
                'name' => '歐美業務子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC049',
            ],
            [
                'name' => 'AI產品子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC050',
            ],
            [
                'name' => '雲端技術子小組',
                'type' => 'subteam',
                'cost_center_code' => 'CC051',
            ],
        ];

        $subTeamIndex = 0;
        foreach ($createdTeams as $team) {
            // 每個小組分配1-2個子小組
            $subTeamsPerTeam = 1;
            if (strpos($team->name, '開發') !== false || strpos($team->name, '研發') !== false) {
                $subTeamsPerTeam = 2; // 開發相關小組有更多子小組
            }

            for ($i = 0; $i < $subTeamsPerTeam; $i++) {
                if ($subTeamIndex < count($subTeams)) {
                    Organizations::create(array_merge($subTeams[$subTeamIndex], [
                        'parent_id' => $team->id,
                        'address' => $team->address,
                        'phone' => $team->phone,
                        'email' => 'subteam' . $subTeamIndex . '@' . str_replace(' ', '', $team->name) . '.com',
                        'monthly_budget' => 200000,
                        'status' => OrganizationStatus::ACTIVE,
                    ]));
                    $subTeamIndex++;
                }
            }
        }

        // 將用戶分配到不同層級的組織
        $users = User::all();
        $admin = User::where('email', 'admin@example.com')->first();

        // 管理員加入總公司
        if ($admin) {
            $admin->organizations()->attach($rootOrg->id);
        }

        // 其他用戶分配到不同層級的組織
        $userIndex = 0;
        $allOrganizations = array_merge([$rootOrg], $createdBranches, $createdDepartments, $createdTeams);

        foreach ($users as $user) {
            if ($user->email === 'admin@example.com') continue;

            // 隨機分配到不同層級的組織
            $orgIndex = $userIndex % count($allOrganizations);
            $user->organizations()->attach($allOrganizations[$orgIndex]->id);
            $userIndex++;
        }
    }
}
