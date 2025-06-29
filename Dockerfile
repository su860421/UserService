# 使用官方 PHP 8.4 CLI 映像檔
FROM php:8.4-cli

# 安裝系統依賴和 PHP 擴展
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 安裝 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 設定工作目錄
WORKDIR /var/www/html

# 複製 composer 檔案
COPY composer.json composer.lock ./

# 安裝 PHP 依賴
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 複製應用程式檔案
COPY . .

# 設定檔案權限
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 啟動 Laravel 開發伺服器
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"] 