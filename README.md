# AI 智能報銷系統 - UserService

基於 **Laravel 12** 和 **PHP 8.2** 開發的企業級用戶管理服務，提供用戶認證、授權管理、組織架構和權限控制功能。

## 🎯 專案定位 

- **用戶認證** - JWT Token 認證、信箱驗證、密碼重設
- **權限管理** - 基於角色的權限控制 (RBAC)
- **組織管理** - 多層級組織架構、成員管理
- **授權服務** - 角色分配、權限驗證

## 📊 開發階段

### 🚀 POC 階段 (概念驗證) - ✅ 已完成
**目標**: 驗證核心功能可行性，支援基本業務流程

**核心功能**:
- ✅ 用戶註冊與認證
- ✅ 組織架構管理
- ✅ 權限控制機制
- ✅ 基本安全防護
- ✅ API 文檔生成

**適用場景**: 內部測試、客戶演示、功能驗證

### 🏢 正式環境 - 🔄 開發中
**目標**: 企業級部署，支援大規模用戶和進階功能

**企業級功能**:
- 🔄 進階安全認證 (OAuth 2.0, SSO, MFA)
- 🔄 批次操作功能
- 🔄 系統監控與審計
- 🔄 資料備份與恢復
- 🔄 高可用性部署

**適用場景**: 生產環境、企業客戶、大規模部署

## 🚀 功能特性

### ✅ POC 階段功能 (已完成)

**🔐 用戶認證**
- JWT Token 認證與刷新
- 信箱驗證機制
- 密碼重設功能
- 帳號啟用/停用管理
- 多裝置登入控制

**👥 用戶管理**
- 用戶 CRUD 操作
- 用戶組織關聯管理
- 員工編號管理
- 用戶狀態控制
- 分頁查詢與篩選

**🏢 組織管理**
- 多層級組織架構
- 組織樹狀結構查詢
- 組織成員管理
- 組織統計資料
- 組織預算管理
- 組織快取機制

**🔑 權限授權**
- 基於角色的權限控制 (RBAC)
- 角色 CRUD 操作
- 權限分配與管理
- 用戶角色分配
- 權限驗證中間件

**📧 通知系統**
- 信箱驗證通知
- 密碼重設通知
- 佇列處理機制
- 通知範本管理

**🛡️ 安全防護**
- 密碼加密儲存
- JWT Token 安全
- 請求驗證與清理
- SQL 注入防護
- XSS 防護
- 統一錯誤處理

### 🔄 正式環境功能 (開發中)

**📊 監控記錄**
- 結構化日誌記錄
- 錯誤追蹤與報告

### ⏳ 企業級功能 (待開發)

**🔐 進階認證授權**
- OAuth 2.0 整合
- SSO 單一登入
- 多因子認證 (MFA)

**👥 進階用戶管理**
- 用戶批次操作（批次啟用/停用）
- 用戶資料匯出功能
- 用戶活動追蹤

**🏢 進階組織管理**
- 組織權限繼承
- 組織審計日誌
- 組織範本管理

**📊 系統管理**
- 系統設定管理 API
- 操作日誌查詢 API
- 資料備份/還原功能
- 資料匯入/匯出功能

## 🏗️ 技術架構

### 核心技術棧
- **PHP 8.2+** - 後端語言
- **Laravel 12** - Web 框架
- **MySQL/PostgreSQL** - 資料庫
- **Redis** - 快取與會話
- **JWT** - 認證機制 (tymon/jwt-auth)
- **Spatie Permission** - 權限管理 (spatie/laravel-permission)
- **Laravel Horizon** - 佇列處理
- **Dedoc Scramble** - API 文檔生成
- **Archtechx Enums** - 列舉管理 (archtechx/enums)
- **Laravel Scaffold** - 程式碼生成 (joesu/laravel-scaffold)
- **Laravel Tinker** - 互動式 REPL
- **Laravel Pail** - 日誌查看工具
- **Laravel Pint** - 程式碼格式化
- **Laravel Sail** - Docker 開發環境

### 專案結構
```
app/
├── Http/                    # 表現層
│   ├── Controllers/        # 控制器
│   ├── Middleware/         # 中介軟體
│   ├── Requests/           # 請求驗證
│   └── Resources/          # API 資源
├── Services/               # 業務邏輯層
├── Repositories/           # 資料存取層
├── Models/                 # 領域模型
├── Contracts/              # 介面定義
├── Notifications/          # 通知系統
├── Enums/                  # 列舉定義
├── Exceptions/             # 自訂異常
└── Observers/              # 模型觀察者
```

### 資料庫設計
- `users` - 用戶基本資料 (ULID 主鍵)
- `organizations` - 組織架構 (ULID 主鍵)
- `organization_user` - 用戶組織關聯
- `permissions` - 權限定義
- `roles` - 角色定義
- `model_has_permissions` - 模型權限關聯
- `model_has_roles` - 模型角色關聯
- `role_has_permissions` - 角色權限關聯

## 📋 API 文檔

本專案使用 **Dedoc Scramble** 自動生成 API 文檔。

### 查看 API 文檔
```bash
# 啟動開發伺服器
php artisan serve

# 瀏覽 API 文檔
http://localhost:8000/api/documentation
```

### 主要 API 端點

**認證相關**
- `POST /api/v1/register` - 用戶註冊
- `POST /api/v1/login` - 用戶登入
- `POST /api/v1/logout` - 用戶登出
- `POST /api/v1/refresh` - 重新整理 Token
- `GET /api/v1/me` - 取得當前用戶
- `POST /api/v1/forgot-password` - 密碼重設
- `POST /api/v1/resend-verification` - 重發驗證信件

**用戶管理**
- `GET /api/v1/users` - 取得用戶清單
- `GET /api/v1/users/{id}` - 取得單一用戶
- `POST /api/v1/users` - 建立用戶
- `PUT /api/v1/users/{id}` - 更新用戶
- `DELETE /api/v1/users/{id}` - 刪除用戶
- `PATCH /api/v1/users/{id}/organizations` - 更新用戶組織

**組織管理**
- `GET /api/v1/organizations` - 取得組織清單
- `GET /api/v1/organizations/tree` - 取得組織樹狀結構
- `GET /api/v1/organizations/{id}/children` - 取得組織子部門
- `GET /api/v1/organizations/{id}/users` - 取得組織成員
- `GET /api/v1/organizations/{id}/stats` - 取得組織統計
- `POST /api/v1/organizations` - 建立組織

**授權管理**
- `GET /api/v1/authorization/permissions` - 取得權限清單
- `GET /api/v1/authorization/roles` - 取得角色清單
- `POST /api/v1/authorization/roles` - 建立角色
- `PUT /api/v1/authorization/users/{id}/roles` - 分配角色給用戶
- `PUT /api/v1/authorization/roles/{id}/permissions` - 分配權限給角色

## 🚀 快速開始

### 環境需求
- **PHP 8.2+**
- **Composer 2.0+**
- **MySQL 8.0+** 或 **PostgreSQL 13+**
- **Redis 6.0+**

### 本地開發

1. **複製專案**
```bash
git clone <repository-url>
cd UserService
```

2. **安裝相依套件**
```bash
composer install
```

3. **環境設定**
```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

4. **資料庫設定**
編輯 `.env` 檔案：
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=user_service
DB_USERNAME=root
DB_PASSWORD=password

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

FRONTEND_URL=http://localhost:3000
```

5. **執行資料庫遷移**
```bash
php artisan migrate
```

6. **執行資料種子**
```bash
php artisan db:seed
```

7. **啟動開發伺服器**
```bash
php artisan serve
```

### Docker 部署

1. **使用 Docker Compose 啟動完整環境**
```bash
docker-compose up -d
```

2. **執行資料庫遷移**
```bash
docker-compose exec app php artisan migrate
```

3. **執行資料種子**
```bash
docker-compose exec app php artisan db:seed
```

## 📊 監控與日誌

### 健康檢查
```http
GET /api/v1/health-check
```

### 佇列監控
- Laravel Horizon 儀表板
- 佇列狀態監控
- 失敗任務處理

## 🔧 開發指南

### 開發工具

**程式碼生成**
```bash
# 使用 Laravel Scaffold 生成 CRUD
php artisan scaffold:make User

# 使用 Tinker 進行互動式開發
php artisan tinker
```

**程式碼品質**
```bash
# 使用 Laravel Pint 格式化程式碼
./vendor/bin/pint

# 使用 Pail 查看日誌
php artisan pail
```

**Docker 開發環境**
```bash
# 使用 Laravel Sail 啟動完整環境
./vendor/bin/sail up

# 執行測試
./vendor/bin/sail test
```

### 程式碼風格
- 遵循 PSR-12 標準
- 使用 Laravel Pint 進行程式碼格式化
- 撰寫完整的 PHPDoc 註解
- 遵循 Laravel 最佳實踐

### 提交規範
- 使用 Conventional Commits
- 撰寫清晰的提交訊息
- 包含相關的 Issue 編號

## 📝 許可證

MIT License

## 🤝 貢獻

歡迎提交 Issue 和 Pull Request！

---

**版本**: 1.0.0  
**最後更新**: 2024年12月  
**維護者**: AI 智能報銷系統開發團隊  
**完成度**: 85% (核心功能)
