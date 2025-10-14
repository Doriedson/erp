FROM php:8.2-fpm-alpine

# Dependências de build e extensões
RUN apk add --no-cache $PHPIZE_DEPS tzdata bash \
 && docker-php-ext-install pdo pdo_mysql

# Timezone (opcional, igual ao da app)
ENV TZ=America/Sao_Paulo
RUN cp /usr/share/zoneinfo/${TZ} /etc/localtime && echo "${TZ}" > /etc/timezone

# Composer (opcional, útil pro seu projeto)
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && rm composer-setup.php

WORKDIR /var/www

# Usuário (opcional): evita permissões chatas
# RUN adduser -D app && chown -R app:app /var/www
# USER app
