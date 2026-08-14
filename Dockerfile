FROM python:3.12-slim AS python

FROM php:8.5-fpm

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

# Copy Python 3.12 from official Python image
COPY --from=python /usr/local /usr/local

# Confirm version
RUN python3 --version

RUN python3 -m venv /opt/venv

ENV PATH="/opt/venv/bin:$PATH"

COPY requirements.txt /tmp/requirements.txt

RUN pip install --upgrade pip \
    && pip install -r /tmp/requirements.txt

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache

CMD ["php-fpm"]
