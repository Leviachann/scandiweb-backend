FROM dunglas/frankenphp

RUN install-php-extensions \
    pdo_mysql \
    mysqli \
    mbstring \
    openssl \
    zip

WORKDIR /app

COPY . .

EXPOSE 8000

CMD ["frankenphp", "run", "--config", "/app/Caddyfile"]
