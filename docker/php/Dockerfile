# docker/php/Dockerfile
FROM php:8.2-fpm-alpine

# --- Args para UID/GID (evitar problemas de permissão no host)
ARG UID=1000
ARG GID=1000

# --- Dependências de build e libs nativas (Alpine)
# PHPIZE_DEPS: ferramentas para compilar extensões (removidas depois)
RUN apk add --no-cache \
    tzdata bash git unzip icu-dev libzip-dev oniguruma-dev \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    $PHPIZE_DEPS

# --- Extensões PHP comuns para apps ERP/Laravel
# intl, zip, bcmath, gd, pdo_mysql e opcache
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
      pdo pdo_mysql intl zip bcmath gd opcache

# --- Redis (via PECL)
RUN pecl install redis \
 && docker-php-ext-enable redis

# --- Timezone
ENV TZ=America/Sao_Paulo
RUN cp /usr/share/zoneinfo/${TZ} /etc/localtime \
 && echo "${TZ}" > /etc/timezone

# --- Composer (mais simples/seguro: copiar da imagem oficial)
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# --- Configurações PHP (ini)
# Ajuste fino para dev; para prod reduza APP_DEBUG e aumente caches conforme necessário
RUN { \
      echo "date.timezone=${TZ}"; \
      echo "memory_limit=512M"; \
      echo "post_max_size=32M"; \
      echo "upload_max_filesize=32M"; \
      echo "max_execution_time=120"; \
      echo "expose_php=0"; \
    } > /usr/local/etc/php/conf.d/app.ini

# --- Opcache recomendado (bom para prod e não atrapalha dev)
RUN { \
      echo "opcache.enable=1"; \
      echo "opcache.enable_cli=1"; \
      echo "opcache.jit=1255"; \
      echo "opcache.jit_buffer_size=64M"; \
      echo "opcache.memory_consumption=256"; \
      echo "opcache.interned_strings_buffer=16"; \
      echo "opcache.max_accelerated_files=20000"; \
      echo "opcache.validate_timestamps=1"; \
      echo "opcache.revalidate_freq=1"; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# --- Usuário não-root para evitar arquivos root:root no host
RUN addgroup -g ${GID} app \
 && adduser  -D -u ${UID} -G app app \
 && mkdir -p /var/www/html/erp \
 && chown -R app:app /var/www

USER app

# --- Diretório de trabalho alinhado ao docker-compose
WORKDIR /var/www/html/erp

# Dica: se quiser ativar Xdebug só em dev, adicione:
# RUN pecl install xdebug && docker-php-ext-enable xdebug
# e controle por variável de ambiente para não ligar em prod.
