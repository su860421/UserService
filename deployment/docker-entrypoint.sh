#!/bin/bash
# deployment/docker-entrypoint.sh
set -e

echo "🚀 Laravel 容器啟動中..."

# 🔥 修复：无论什么环境都先清除缓存
echo "🧹 清除 Laravel 缓存..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# 根據環境執行不同邏輯
if [ "$APP_ENV" = "production" ]; then
    echo "🏭 生產環境模式"
    
    # 等待資料庫
    if [ -n "$DB_HOST" ]; then
        echo "⏳ 等待資料庫 $DB_HOST:${DB_PORT:-3306}..."
        while ! nc -z "$DB_HOST" "${DB_PORT:-3306}"; do
            sleep 2
        done
        echo "✅ 資料庫連接成功"
    fi
    
    # 🔥 修复：等待Redis
    if [ -n "$REDIS_HOST" ]; then
        echo "⏳ 等待 Redis $REDIS_HOST:${REDIS_PORT:-6379}..."
        while ! nc -z "$REDIS_HOST" "${REDIS_PORT:-6379}"; do
            sleep 2
        done
        echo "✅ Redis 連接成功"
    fi
    
    # 設置生產環境權限
    echo "🔧 設置生產環境權限..."
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
    
    # Laravel 優化
    echo "⚡ Laravel 生產環境優化..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    # 執行資料庫遷移（只在 app 容器中執行一次）
    if [ "${CONTAINER_ROLE:-app}" = "app" ]; then
        echo "📊 檢查資料庫遷移..."
        php artisan migrate --force
    fi
else
    echo "🔧 開發環境模式"
    
    # 🔥 修复：开发环境也等待依赖服务
    if [ -n "$DB_HOST" ]; then
        echo "⏳ 等待資料庫 $DB_HOST:${DB_PORT:-3306}..."
        while ! nc -z "$DB_HOST" "${DB_PORT:-3306}"; do
            sleep 2
        done
        echo "✅ 資料庫連接成功"
    fi
    
    if [ -n "$REDIS_HOST" ]; then
        echo "⏳ 等待 Redis $REDIS_HOST:${REDIS_PORT:-6379}..."
        while ! nc -z "$REDIS_HOST" "${REDIS_PORT:-6379}"; do
            sleep 2
        done
        echo "✅ Redis 連接成功"
    fi
    
    # 開發環境權限設置
    chmod -R 777 storage bootstrap/cache 2>/dev/null || true
    
    # 🔥 修复：开发环境也执行数据库迁移
    if [ "${CONTAINER_ROLE:-app}" = "app" ]; then
        echo "📊 檢查資料庫遷移..."
        php artisan migrate --force
    fi
fi

echo "✅ 容器啟動完成"

# 根據容器角色執行不同命令
case "${CONTAINER_ROLE:-app}" in
    "app")
        exec php-fpm
        ;;
    "worker")
        exec php artisan queue:work --verbose --tries=3 --timeout=90
        ;;
    "scheduler")
        exec sh -c 'while true; do php artisan schedule:run; sleep 60; done'
        ;;
    "horizon")
        exec php artisan horizon
        ;;
    *)
        exec php-fpm
        ;;
esac