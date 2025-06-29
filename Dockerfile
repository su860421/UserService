# 使用 PHP 8.4 基礎鏡像
FROM php:8.4-cli

# 安裝系統依賴和 PHP 擴展
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# 安裝 PHP 擴展
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 設置 PHP 內存限制和執行時間（使用多種方式確保設置生效）
RUN echo "memory_limit = 2G" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "memory_limit = 2G" >> /usr/local/etc/php/php.ini \
    && echo "php_value[memory_limit] = 2G" >> /usr/local/etc/php-fpm.conf \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/docker-php-timeout.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/php.ini

# 安裝 Redis 擴展
RUN pecl install redis && docker-php-ext-enable redis

# 安裝 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 複製 composer 文件
COPY composer.json composer.lock ./

# 設置目錄權限
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 安裝 PHP 依賴
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader

# 複製專案文件
COPY . .

# 生成 autoload 文件
RUN composer dump-autoload --no-scripts --optimize

# 安裝並編譯前端資源
RUN npm install && npm run build

# 最終權限設置
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 設置啟動命令
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
