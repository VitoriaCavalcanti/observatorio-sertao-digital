FROM php:8.4-cli

WORKDIR /app

RUN apt-get update \
    && apt-get install -y \
        git \
        unzip \
        libpq-dev \
        libicu-dev \
    && docker-php-ext-install intl pdo pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

COPY . .

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
