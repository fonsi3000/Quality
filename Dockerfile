FROM ubuntu:22.04

# Variables de entorno base
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
ENV NODE_ENV=production
ENV VITE_HMR_HOST=localhost
ENV APP_URL=http://localhost

# Variables de entorno para Laravel
ENV APP_NAME=Quality
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV DB_CONNECTION=mysql
ENV DB_HOST=127.0.0.1
ENV DB_PORT=3306
ENV DB_DATABASE=quality_db
ENV DB_USERNAME=root
ENV DB_PASSWORD=E5pum452025*.

RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y bash git sudo openssh-client \
    libxml2-dev libonig-dev autoconf gcc g++ make \
    libfreetype6-dev libjpeg-turbo8-dev libpng-dev libzip-dev \
    curl unzip nano software-properties-common

# Instalación de Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm@latest

# Configuración de MySQL
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
    mysql -e "CREATE DATABASE quality_db;" && \
    mysql -e "CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'E5pum452025*.';" && \
    mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;" && \
    mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;" && \
    mysql -e "FLUSH PRIVILEGES;"

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

# Copiar archivos de la aplicación
COPY . .

# Instalación de dependencias de Node.js y build de assets
RUN npm install && \
    npm install --save-dev laravel-vite-plugin@latest && \
    npm install preline --save && \
    npm run build && \
    rm -rf node_modules && \
    npm ci --omit=dev

# Instalación de dependencias de Composer
RUN composer install --no-interaction --no-dev --optimize-autoloader && \
    composer require laravel/octane --with-all-dependencies

# Crear y configurar archivo .env
RUN echo "APP_NAME=${APP_NAME}\n\
APP_ENV=${APP_ENV}\n\
APP_KEY=\n\
APP_DEBUG=${APP_DEBUG}\n\
APP_URL=${APP_URL}\n\
ASSET_URL=${APP_URL}\n\
APP_TIMEZONE=${TZ}\n\
\n\
LOG_CHANNEL=stack\n\
LOG_DEPRECATIONS_CHANNEL=null\n\
LOG_LEVEL=debug\n\
\n\
DB_CONNECTION=${DB_CONNECTION}\n\
DB_HOST=${DB_HOST}\n\
DB_PORT=${DB_PORT}\n\
DB_DATABASE=${DB_DATABASE}\n\
DB_USERNAME=${DB_USERNAME}\n\
DB_PASSWORD=${DB_PASSWORD}\n\
\n\
BROADCAST_DRIVER=log\n\
CACHE_DRIVER=file\n\
FILESYSTEM_DISK=local\n\
QUEUE_CONNECTION=sync\n\
SESSION_DRIVER=file\n\
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
MAIL_FROM_NAME='\${APP_NAME}'\n\
\n\
VITE_APP_NAME='\${APP_NAME}'\n\
VITE_HMR_HOST=localhost\n\
VITE_APP_URL='\${APP_URL}'\n\
\n\
OCTANE_SERVER=swoole\n" > .env

# Generar clave de aplicación
RUN php artisan key:generate --force

# Configuración de permisos
RUN php artisan storage:link && \
    chown -R www-data:www-data /app && \
    chmod -R 775 storage bootstrap/cache && \
    chmod -R 775 public

# Script de inicio
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
echo "Iniciando MySQL..."\n\
service mysql start\n\
\n\
echo "Esperando que MySQL esté listo..."\n\
while ! mysqladmin ping -h"localhost" -u"root" -p"E5pum452025*." --silent; do\n\
    sleep 1\n\
done\n\
\n\
echo "Ejecutando migraciones..."\n\
php artisan migrate --force\n\
\n\
echo "Iniciando Octane con Swoole..."\n\
php artisan octane:start --server=swoole --host=0.0.0.0 --port=80\n\
' > /app/start.sh && chmod +x /app/start.sh

CMD ["/app/start.sh"]

EXPOSE 80 5173