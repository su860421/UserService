# UserSeeder 使用說明

## 概述
UserSeeder 用於建立測試用的用戶資料，包含：
- 1 個超級管理員
- 10 個已驗證的一般用戶
- 1 個未驗證的用戶（用於測試）
- 1 個停用的用戶（用於測試）

## 建立的用戶資料

### 超級管理員
- **帳號**: admin@example.com
- **密碼**: admin123
- **員工編號**: ADMIN001
- **狀態**: 已驗證、啟用

### 一般用戶（已驗證）
所有一般用戶的密碼都是 `password123`，包含以下用戶：

1. **張小明** - zhang.xiaoming@example.com (EMP001)
2. **李小華** - li.xiaohua@example.com (EMP002)
3. **王小美** - wang.xiaomei@example.com (EMP003)
4. **陳大強** - chen.daqiang@example.com (EMP004)
5. **林小芳** - lin.xiaofang@example.com (EMP005)
6. **黃志明** - huang.zhiming@example.com (EMP006)
7. **劉雅婷** - liu.yating@example.com (EMP007)
8. **吳建志** - wu.jianzhi@example.com (EMP008)
9. **許淑芬** - xu.shufen@example.com (EMP009)
10. **蔡明德** - cai.mingde@example.com (EMP010)

### 測試用用戶
- **未驗證用戶**: unverified@example.com (EMP011) - 密碼: password123
- **停用用戶**: disabled@example.com (EMP012) - 密碼: password123

## 執行方式

### 執行所有 Seeder
```bash
php artisan db:seed
```

### 只執行 UserSeeder
```bash
php artisan db:seed --class=UserSeeder
```

### 重新建立資料庫並執行 Seeder
```bash
php artisan migrate:fresh --seed
```

## 注意事項
1. 所有用戶的電子郵件都已驗證（除了未驗證用戶）
2. 所有用戶都是啟用狀態（除了停用用戶）
3. 密碼都已經過雜湊處理
4. 每個用戶都有唯一的員工編號
5. 所有用戶都有手機號碼

## 自訂修改
如果需要修改用戶資料，請編輯 `database/seeders/UserSeeder.php` 檔案中的 `$users` 陣列。 