# ========================
# STAGE 1: Build Laravel
# ========================
FROM php:8.3-fpm AS builder

# Cài đặt các gói hệ thống & extension PHP cần thiết
RUN apt-get update && apt-get install -y \
    git zip unzip libzip-dev libpng-dev libonig-dev libxml2-dev libjpeg-dev libfreetype6-dev \
    curl supervisor nginx nodejs npm && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Cài Composer (từ image chính thức)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files trước để tận dụng cache Docker
COPY composer.json composer.lock ./

# Cài đặt thư viện PHP (production)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Copy toàn bộ code dự án vào container
COPY . .

# Build frontend (nếu có Laravel Mix / Vite)
RUN if [ -f package.json ]; then npm install && npm run build; fi

# Cache và optimize Laravel
RUN php artisan config:clear && \
    php artisan cache:clear && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan config:cache || true

# ========================
# STAGE 2: Production
# ========================
FROM php:8.3-fpm

# Cài đặt Nginx và Supervisor để chạy PHP-FPM
RUN apt-get update && apt-get install -y nginx supervisor && \
    docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy app đã build từ stage builder
COPY --from=builder /var/www/html /var/www/html
COPY --from=builder /usr/bin/composer /usr/bin/composer

# Copy file cấu hình nginx và supervisor

# Thiết lập quyền cho storage và cache
WORKDIR /var/www/html
RUN mkdir -p storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

EXPOSE 80

# Chạy startup script
CMD ["/start.sh"]
