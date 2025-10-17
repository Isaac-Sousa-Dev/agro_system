FROM php:8.2-fpm-alpine

# Instalar dependências do sistema
RUN apk add --no-cache \
    postgresql-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl

# Instalar extensões do PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        gd \
        zip \
        opcache

# Instalar o Composer
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
RUN chown -R www-data:www-data /var/www/html

# Configurar PHP para desenvolvimento
RUN echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/docker-php-memory.ini