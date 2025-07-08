FROM php:8.4-fpm

# 安裝系統依賴和 PHP 擴展 (這部分完全不變)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    netcat-openbsd 

# 安裝 PHP 擴展 (這部分完全不變)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN pecl install redis && docker-php-ext-enable redis

# 安裝 Composer (這部分完全不變)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# --- 修改二：將複製專案文件的時機提前 ---
# 我們先將所有專案檔案複製進來。
# 這樣，後續的 `npm install` 就能找到 `package-lock.json`，
# `composer install` 也能找到 `composer.lock`。
COPY . .

# 設置目錄權限 (提前執行，確保後續步驟有權限)
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 安裝 PHP 依賴
# 這時它會在容器內，基於您複製進來的 composer.lock 建立一個乾淨的 vendor 目錄
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader

# 生成 autoload 文件 (這部分完全不變)
RUN composer dump-autoload --no-scripts --optimize

# 安裝並編譯前端資源
# 這時它會在容器內，基於您複製進來的 package-lock.json 建立一個乾淨的 node_modules 目錄
RUN npm install && npm run build

# 最終權限設置 (可以再次執行以確保萬無一失)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# ========== 新增：生產環境啟動腳本 ==========
COPY deployment/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# 新增：健康檢查腳本
COPY deployment/health-check.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/health-check.sh

# 健康檢查
HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD /usr/local/bin/health-check.sh

# 使用自定義啟動腳本
CMD ["/usr/local/bin/docker-entrypoint.sh"]