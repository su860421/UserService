#!/bin/bash
# deployment/health-check.sh

# 檢查 PHP-FPM 進程
if ! pgrep -f php-fpm > /dev/null; then
    exit 1
fi

# 檢查 Laravel 應用狀態
php -r "
try {
    // 檢查基本的 Laravel 功能
    require_once '/var/www/html/vendor/autoload.php';
    \$app = require_once '/var/www/html/bootstrap/app.php';
    \$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
    \$kernel->bootstrap();
    
    // 檢查資料庫連接
    if (getenv('DB_HOST')) {
        \$pdo = new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: 3306),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
    }
    
    // 檢查 Redis 連接
    if (getenv('REDIS_HOST') && class_exists('Redis')) {
        \$redis = new Redis();
        \$redis->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT') ?: 6379);
        \$redis->ping();
    }
    
    echo 'healthy';
} catch (Exception \$e) {
    exit(1);
}
"