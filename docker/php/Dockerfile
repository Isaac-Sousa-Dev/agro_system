# /sistema-agro/docker/php/Dockerfile

FROM php:8.2-fpm-alpine

# Instalar dependências necessárias para o Laravel e PostgreSQL
RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Instalar o Composer (gerenciador de dependências do PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Criar usuário com UID/GID que corresponde ao usuário do host
ARG USER_ID=1000
ARG GROUP_ID=1000

# Modificar usuário www-data existente para usar os IDs do host
RUN deluser www-data && \
    addgroup -g ${GROUP_ID} www-data && \
    adduser -D -u ${USER_ID} -G www-data www-data

# Altera o dono do diretório para o usuário www-data
# (necessário para permissões de escrita do Laravel)
RUN chown -R www-data:www-data /var/www/html