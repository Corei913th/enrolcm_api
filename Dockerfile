FROM php:8.2-fpm

# System dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    libmagickwand-dev \
    ghostscript \
    tesseract-ocr \
    tesseract-ocr-fra \
    tesseract-ocr-eng \
    nodejs \
    npm \
    chromium \
    chromium-driver \
    libnss3 \
    libnspr4 \
    libatk1.0-0 \
    libatk-bridge2.0-0 \
    libcups2 \
    libdrm2 \
    libdbus-1-3 \
    libxkbcommon0 \
    libxcomposite1 \
    libxdamage1 \
    libxrandr2 \
    libgbm1 \
    libpango-1.0-0 \
    libcairo2 \
    libasound2t64 \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip

RUN pecl install imagick && docker-php-ext-enable imagick

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

ENV PUPPETEER_SKIP_DOWNLOAD=true \
    PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium \
    BROWSERSHOT_CHROME_PATH=/usr/bin/chromium

COPY . /var/www

COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini

RUN cp .env.example .env || true

# Install npm deps (puppeteer for browsershot) and build assets
RUN npm install --omit=dev && npm run build --if-present

# Composer install & Laravel caches
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts && \
    php artisan event:cache && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

USER www-data

EXPOSE 9000
CMD ["php-fpm"]
