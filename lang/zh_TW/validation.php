<?php

return [
    'auth' => [
        'name' => [
            'required' => '姓名為必填欄位',
            'max' => '姓名不能超過 255 個字元',
        ],
        'email' => [
            'required' => '電子郵件為必填欄位',
            'email' => '請輸入有效的電子郵件地址',
            'unique' => '此電子郵件地址已被使用',
            'exists' => '找不到該電子郵件地址的用戶',
        ],
        'password' => [
            'required' => '密碼為必填欄位',
            'min' => '密碼至少需要 8 個字元',
            'confirmed' => '密碼確認不符',
        ],
        'current_password' => [
            'required' => '目前密碼為必填欄位',
        ],
        'phone' => [
            'max' => '電話號碼不能超過 20 個字元',
        ],
        'employee_id' => [
            'unique' => '此員工編號已被使用',
        ],
        'token' => [
            'required' => '重設密碼 token 為必填欄位',
            'min' => '重設密碼 token 長度不足',
        ],
    ],
    'user' => [
        'name' => [
            'required' => '姓名為必填欄位',
            'max' => '姓名不能超過 255 個字元',
        ],
        'email' => [
            'required' => '電子郵件為必填欄位',
            'email' => '請輸入有效的電子郵件格式',
            'unique' => '此電子郵件已被使用',
        ],
        'password' => [
            'required' => '密碼為必填欄位',
            'confirmed' => '密碼確認不符',
        ],
        'phone' => [
            'max' => '電話號碼不能超過 20 個字元',
        ],
        'employee_id' => [
            'max' => '員工編號不能超過 50 個字元',
            'unique' => '此員工編號已被使用',
        ],
        'is_active' => [
            'boolean' => '啟用狀態必須為布林值',
        ],
    ],
    'pagination' => [
        'per_page' => [
            'min' => '每頁顯示數量最少為 1',
            'max' => '每頁顯示數量最多為 100',
        ],
        'page' => [
            'min' => '頁碼最少為 1',
        ],
    ],
    'sorting' => [
        'order_by' => [
            'in' => '排序欄位不正確',
        ],
        'order_direction' => [
            'in' => '排序方向必須為 asc 或 desc',
        ],
    ],
    'filtering' => [
        'filters' => [
            'array' => '過濾條件必須為陣列格式',
        ],
        'filters_field' => [
            'in' => '指定的過濾欄位不存在',
        ],
        'filters_operator' => [
            'in' => '指定的過濾操作符不正確',
        ],
        'filters_value' => [
            'required' => '過濾值為必填',
        ],
        'search' => [
            'string' => '搜尋關鍵字必須為字串',
        ],
    ],
    'selection' => [
        'columns' => [
            'array' => '欄位選擇必須為陣列格式',
        ],
        'columns_each' => [
            'in' => '指定的欄位不存在',
        ],
        'with' => [
            'array' => '關聯載入必須為陣列格式',
        ],
        'with_each' => [
            'in' => '指定的關聯不存在',
        ],
    ],
    'role' => [
        'name' => [
            'required' => '角色名稱為必填欄位',
            'unique' => '角色名稱已存在',
        ],
        'permissions' => [
            'required' => '權限為必填欄位',
            'exists' => '所選權限不存在',
        ],
    ],
    'auth.password_reset' => [
        'invalid_token' => '密碼重設連結無效或已過期',
    ],
];
