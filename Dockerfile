# 使用官方 PHP 8.4 FPM 映像檔
FROM php:8.4-fpm

# 設定工作目錄
WORKDIR /var/www/html

# 安裝系統依賴
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# 安裝 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 複製所有應用程式檔案
COPY . .

# 安裝 PHP 依賴
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 設定檔案權限
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# 建立 .env 檔案（如果不存在）
RUN if [ ! -f .env ]; then cp .env.example .env 2>/dev/null || echo "APP_NAME=Laravel" > .env; fi

# 生成應用程式金鑰
RUN php artisan key:generate --no-interaction || true

# 暴露端口
EXPOSE 9000

# 啟動 PHP-FPM
CMD ["php-fpm"] 