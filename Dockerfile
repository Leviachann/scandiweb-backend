FROM dunglas/frankenphp

RUN install-php-extensions \
    pdo_mysql \
    mysqli \
    mbstring \
    openssl \
    zip

COPY . /app

WORKDIR /app

ENV PORT=8000

CMD ["frankenphp", "run", "--config", "/app/Caddyfile"]
