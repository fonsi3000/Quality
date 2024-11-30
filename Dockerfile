FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
ENV NODE_ENV=production

# Instalación de dependencias base
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y bash git curl \
    libxml2-dev libonig-dev \
    libfreetype6-dev libjpeg-turbo8-dev libpng-dev libzip-dev \
    software-properties-common netcat

# Instalación de Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm@latest

# Instalación de PHP
RUN add-apt-repository ppa:ondrej/php -y && \
    apt-get update && \
    apt-get install -y php8.2 php8.2-fpm php8.2-cli php8.2-common \
    php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath \
    php8.2-intl php8.2-dev

# Instalación de Swoole
RUN pecl install swoole && \
    echo "extension=swoole.so" > /etc/php/8.2/mods-available/swoole.ini && \
    phpenmod swoole

# Instalación de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY . .

# Instalación simple de dependencias
RUN npm install && npm run build

# Instalación de dependencias PHP
RUN composer install --no-interaction --optimize-autoloader && \
    composer require laravel/octane --with-all-dependencies

# Configuración y permisos
RUN php artisan storage:link && \
    chown -R www-data:www-data /app && \
    chmod -R 775 storage bootstrap/cache

CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=80"]

EXPOSE 80