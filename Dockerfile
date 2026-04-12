FROM dunglas/frankenphp

RUN install-php-extensions \
    pdo_mysql \
    mysqli \
    mbstring \
    openssl \
    zip

COPY . /app

WORKDIR /app
