FROM php:8.4-fpm AS php_base
WORKDIR /var/www/html

# 安裝系統依賴套件
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# 安裝 PHP 核心擴展
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# 安裝 Redis 擴展
RUN pecl install redis && docker-php-ext-enable redis

# 安裝最新版的 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 僅複製 composer 的定義檔案
COPY composer.json composer.lock ./

# 安裝 PHP 依賴套件。這會在一個乾淨的環境中生成 vendor 目錄
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader


# ---- 階段二：Node.js 依賴與前端資源編譯 ----
# 使用一個輕量的 Node.js 版本作為基礎，並命名為 node_builder
FROM node:20-alpine AS node_builder
WORKDIR /var/www/html

# 僅複製 package.json 的定義檔案
COPY package.json package-lock.json ./

# 安裝 Node.js 依賴套件，生成 node_modules 目錄
RUN npm install

# 複製所有應用程式碼，以便編譯前端資源
COPY . .

# 執行編譯指令
RUN npm run build


# ---- 最終階段：組合出正式環境的映像檔 ----
# 以我們第一階段的 php_base 作為基礎，它已經包含了 PHP 和 vendor 目錄
FROM php_base AS production
WORKDIR /var/www/html

# 從 php_base 階段，複製已經安裝好的 vendor 目錄進來
COPY --from=php_base /var/www/html/vendor ./vendor

# 複製我們自己的應用程式原始碼 (這時不會與 vendor 衝突)
COPY . .

# 從 node_builder 階段，只複製編譯好的前端資源成品進來
COPY --from=node_builder /var/www/html/public/build ./public/build

# 產生優化的 autoload 檔案
RUN composer dump-autoload --no-scripts --optimize

# 設定 Laravel 快取，提升效能
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# 設定最終的檔案權限，確保網頁伺服器可以寫入
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 開放 9000 port 並啟動 php-fpm 服務
EXPOSE 9000
CMD ["php-fpm"]