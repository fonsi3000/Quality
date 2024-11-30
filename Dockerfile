FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
ENV NODE_ENV=production
ENV VITE_HMR_HOST=localhost

# Instalación de dependencias base
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y bash git sudo openssh-client \
    libxml2-dev libonig-dev autoconf gcc g++ make \
    libfreetype6-dev libjpeg-turbo8-dev libpng-dev libzip-dev \
    curl unzip nano software-properties-common netcat

# Instalación de Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm@latest

# Instalación de PHP y extensiones
RUN add-apt-repository ppa:ondrej/php -y && \
    apt-get update && \
    apt-get install -y php8.2 php8.2-fpm php8.2-cli php8.2-common \
    php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath \
    php8.2-intl php8.2-readline php8.2-pcov php8.2-dev

# Instalación de Swoole
RUN pecl install swoole && \
    echo "extension=swoole.so" > /etc/php/8.2/mods-available/swoole.ini && \
    phpenmod swoole

# Instalación de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Copiar archivos de configuración npm
COPY package*.json ./
COPY vite.config.js ./

# Instalación de dependencias Node.js y build
RUN npm ci && \
    npm install -D vite && \
    npm install --save-dev laravel-vite-plugin@latest && \
    npm install preline --save

# Copiar el resto de archivos
COPY . .

# Build de assets
RUN npm run build && \
    rm -rf node_modules && \
    npm ci --omit=dev

# Instalación de dependencias PHP
RUN composer install --no-interaction --no-dev --optimize-autoloader && \
    composer require laravel/octane --with-all-dependencies

# Configuración de permisos
RUN chmod -R 775 storage bootstrap/cache public && \
    chown -R www-data:www-data /app

# Configuración del storage link
RUN php artisan storage:link

# Script de inicio
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]

EXPOSE 80