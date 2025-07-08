#!/bin/bash
# deployment/docker-entrypoint.sh
set -e

echo "🚀 Laravel 容器啟動中..."

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
    # 開發環境保持簡單
    chmod -R 777 storage bootstrap/cache 2>/dev/null || true
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
    *)
        exec php-fpm
        ;;
esac