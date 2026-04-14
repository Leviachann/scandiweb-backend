FROM php:8.2-apache

RUN apt-get update && apt-get install -y libonig-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring

COPY . /var/www/html/

COPY apache.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

EXPOSE 80

CMD ["apache2-foreground"]
