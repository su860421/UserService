<?php

return [
    'auth' => [
        'name' => '姓名',
        'email' => '電子郵件',
        'password' => '密碼',
        'current_password' => '目前密碼',
        'phone' => '電話',
        'employee_id' => '員工編號',
        'token' => '重設密碼 token',
    ],
    'user' => [
        'name' => '姓名',
        'email' => '電子郵件',
        'password' => '密碼',
        'phone' => '電話',
        'employee_id' => '員工編號',
        'is_active' => '啟用狀態',
    ],
    'pagination' => [
        'per_page' => '每頁顯示數量',
        'page' => '頁碼',
    ],
    'sorting' => [
        'order_by' => '排序欄位',
        'order_direction' => '排序方向',
    ],
    'filtering' => [
        'filters' => '過濾條件',
        'search' => '搜尋關鍵字',
    ],
    'selection' => [
        'columns' => '欄位選擇',
        'with' => '關聯載入',
    ],
    'role' => [
        'name' => '角色名稱',
        'description' => '角色描述',
        'permissions' => '權限',
    ],
    'organiztions' => [
        'name' => '部門名稱',
        'type' => '部門類型',
        'parent_id' => '上層部門',
        'manager_user_id' => '部門主管',
        'address' => '地址',
        'phone' => '電話',
        'email' => '信箱',
        'monthly_budget' => '月預算',
        'approval_settings' => '審核設定',
        'settings' => '組織設定',
        'cost_center_code' => '成本中心代碼',
        'status' => '狀態',
    ],
];
