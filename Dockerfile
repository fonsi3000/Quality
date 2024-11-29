FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive
ENV NODE_ENV=production
ENV TZ=UTC

# Sistema base y utilidades
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y \
    bash \
    git \
    sudo \
    openssh-client \
    libxml2-dev \
    libonig-dev \
    autoconf \
    gcc \
    g++ \
    make \
    libfreetype6-dev \
    libjpeg-turbo8-dev \
    libpng-dev \
    libzip-dev \
    curl \
    unzip \
    nano \
    software-properties-common \
    default-mysql-client \
    sqlite3 \
    cron

# Node.js y NPM
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm@latest

# MySQL
RUN apt-get install -y mysql-server && \
    mkdir -p /var/run/mysqld /var/lib/mysql && \
    chown -R mysql:mysql /var/run/mysqld /var/lib/mysql && \
    echo "[mysqld]" >> /etc/mysql/my.cnf && \
    echo "user = mysql" >> /etc/mysql/my.cnf && \
    echo "bind-address = 0.0.0.0" >> /etc/mysql/my.cnf && \
    echo "default-authentication-plugin = mysql_native_password" >> /etc/mysql/my.cnf && \
    echo "skip-host-cache" >> /etc/mysql/my.cnf && \
    echo "skip-name-resolve" >> /etc/mysql/my.cnf && \
    echo "max_allowed_packet = 256M" >> /etc/mysql/my.cnf && \
    service mysql start && \
    sleep 5 && \
    mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'E5pum452025*.';" && \
    mysql -u root -pE5pum452025*. -e "CREATE DATABASE quality_db;" && \
    mysql -u root -pE5pum452025*. -e "CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'E5pum452025*.';" && \
    mysql -u root -pE5pum452025*. -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;" && \
    mysql -u root -pE5pum452025*. -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;" && \
    mysql -u root -pE5pum452025*. -e "FLUSH PRIVILEGES;"

# PHP y extensiones
RUN add-apt-repository ppa:ondrej/php -y && \
    apt-get update && \
    apt-get install -y \
    php8.2 \
    php8.2-fpm \
    php8.2-cli \
    php8.2-common \
    php8.2-mysql \
    php8.2-zip \
    php8.2-gd \
    php8.2-mbstring \
    php8.2-curl \
    php8.2-xml \
    php8.2-bcmath \
    php8.2-intl \
    php8.2-readline \
    php8.2-pcov \
    php8.2-dev \
    php8.2-sqlite3 \
    php8.2-redis

# Swoole
RUN pecl install swoole && \
    echo "extension=swoole.so" > /etc/php/8.2/mods-available/swoole.ini && \
    phpenmod swoole

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Copia archivos de configuración primero para aprovechar la caché de Docker
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Node.js dependencies
COPY package*.json ./
RUN npm ci

# Código fuente
COPY . .

# Crear y configurar el archivo .env
RUN echo "APP_NAME=Quality\n\
APP_ENV=production\n\
APP_KEY=\n\
APP_DEBUG=false\n\
APP_URL=http://localhost\n\
\n\
LOG_CHANNEL=stack\n\
LOG_DEPRECATIONS_CHANNEL=null\n\
LOG_LEVEL=debug\n\
\n\
DB_CONNECTION=mysql\n\
DB_HOST=127.0.0.1\n\
DB_PORT=3306\n\
DB_DATABASE=quality_db\n\
DB_USERNAME=root\n\
DB_PASSWORD=E5pum452025*.\n\
\n\
BROADCAST_DRIVER=log\n\
CACHE_DRIVER=file\n\
FILESYSTEM_DISK=local\n\
QUEUE_CONNECTION=sync\n\
SESSION_DRIVER=database\n\
SESSION_LIFETIME=120\n\
\n\
MEMCACHED_HOST=127.0.0.1\n\
\n\
REDIS_HOST=127.0.0.1\n\
REDIS_PASSWORD=null\n\
REDIS_PORT=6379\n\
\n\
MAIL_MAILER=smtp\n\
MAIL_HOST=mailpit\n\
MAIL_PORT=1025\n\
MAIL_USERNAME=null\n\
MAIL_PASSWORD=null\n\
MAIL_ENCRYPTION=null\n\
MAIL_FROM_ADDRESS='hello@example.com'\n\
MAIL_FROM_NAME='${APP_NAME}'\n\
\n\
AWS_ACCESS_KEY_ID=\n\
AWS_SECRET_ACCESS_KEY=\n\
AWS_DEFAULT_REGION=us-east-1\n\
AWS_BUCKET=\n\
AWS_USE_PATH_STYLE_ENDPOINT=false\n\
\n\
PUSHER_APP_ID=\n\
PUSHER_APP_KEY=\n\
PUSHER_APP_SECRET=\n\
PUSHER_HOST=\n\
PUSHER_PORT=443\n\
PUSHER_SCHEME=https\n\
PUSHER_APP_CLUSTER=mt1\n\
\n\
VITE_APP_NAME='${APP_NAME}'\n\
VITE_PUSHER_APP_KEY='${PUSHER_APP_KEY}'\n\
VITE_PUSHER_HOST='${PUSHER_HOST}'\n\
VITE_PUSHER_PORT='${PUSHER_PORT}'\n\
VITE_PUSHER_SCHEME='${PUSHER_SCHEME}'\n\
VITE_PUSHER_APP_CLUSTER='${PUSHER_APP_CLUSTER}'\n\
\n\
OCTANE_SERVER=swoole" > .env

# Generación de key y optimizaciones
RUN php artisan key:generate --force && \
    composer dump-autoload --optimize

# Build de assets y limpieza
RUN npm install && \
    npm install -D vite && \
    npm run build && \
    rm -rf node_modules && \
    npm cache clean --force

# Laravel Octane
RUN composer require laravel/octane --no-interaction && \
    php artisan octane:install --server=swoole

# Optimizaciones de Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan event:cache

# Storage link y permisos
RUN php artisan storage:link && \
    chown -R www-data:www-data /app && \
    chmod -R 775 storage bootstrap/cache && \
    chmod -R 775 public

# Script de inicio mejorado
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
echo "Configurando MySQL..."\n\
mkdir -p /var/run/mysqld /var/lib/mysql\n\
chown -R mysql:mysql /var/run/mysqld /var/lib/mysql\n\
\n\
echo "Iniciando MySQL..."\n\
service mysql start\n\
\n\
echo "Esperando que MySQL esté listo..."\n\
for i in {1..30}; do\n\
    if mysqladmin ping -h"localhost" -u"root" -p"E5pum452025*." --silent; then\n\
        echo "MySQL está listo"\n\
        break\n\
    fi\n\
    echo "Intento $i/30..."\n\
    sleep 1\n\
done\n\
\n\
if ! mysqladmin ping -h"localhost" -u"root" -p"E5pum452025*." --silent; then\n\
    echo "MySQL no pudo iniciarse correctamente"\n\
    exit 1\n\
fi\n\
\n\
echo "Ejecutando migraciones..."\n\
php artisan migrate --force\n\
\n\
echo "Limpiando y optimizando caché..."\n\
php artisan optimize:clear\n\
php artisan optimize\n\
\n\
echo "Iniciando Laravel Octane..."\n\
php artisan octane:start --server=swoole --host=0.0.0.0 --port=80 --workers=4 --task-workers=2 --max-requests=1000\n\
' > /app/start.sh && chmod +x /app/start.sh

# Limpieza final
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

CMD ["/app/start.sh"]
EXPOSE 80