FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libonig-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli mbstring

RUN a2enmod rewrite

COPY . /var/www/html/

RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf

EXPOSE ${PORT:-80}

CMD ["apache2-foreground"]
