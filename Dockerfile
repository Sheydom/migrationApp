# -------------------------
# Stage 1: PHP / Composer
# -------------------------
FROM php:8.5-fpm AS php-build

RUN apt-get update && apt-get install -y \
    poppler-utils \
    libgl1 \
    libglib2.0-0 \
    git \
    unzip \
    curl \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    && docker-php-ext-install \
        pdo_mysql \
        zip \
        intl \
        bcmath \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction


# -------------------------
# Stage 2: Build Vite assets
# -------------------------
FROM node:22 AS frontend-build

WORKDIR /var/www/html

COPY package.json package-lock.json ./

RUN npm ci

# Vite needs the Laravel source AND vendor CSS from Filament / Flux
COPY --from=php-build /var/www/html /var/www/html

RUN npm run build


# -------------------------
# Stage 3: Final app image
# -------------------------
FROM php:8.5-fpm

RUN apt-get update && apt-get install -y \
    poppler-utils \
    libgl1 \
    libglib2.0-0 \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install \
        pdo_mysql \
        zip \
        intl \
        bcmath \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Laravel + vendor
COPY --from=php-build /var/www/html /var/www/html

# Compiled Vite assets
COPY --from=frontend-build /var/www/html/public/build /var/www/html/public/build

RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache

CMD ["php-fpm"]
