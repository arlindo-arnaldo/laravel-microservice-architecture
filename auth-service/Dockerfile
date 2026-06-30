FROM php:8.3-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-interaction --optimize-autoloader

RUN cp .env.example .env && php artisan key:generate && php artisan jwt:secret --force

CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && php artisan l5-swagger:generate && php artisan serve --host=0.0.0.0 --port=8000"]
