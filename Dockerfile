# Usa Ubuntu 22.04 como imagen base
FROM ubuntu:22.04

# Evita interacciones durante la instalación de paquetes
ENV DEBIAN_FRONTEND=noninteractive

# Actualiza el sistema e instala paquetes necesarios
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y bash git sudo openssh-client \
    libxml2-dev libonig-dev autoconf gcc g++ make \
    libfreetype6-dev libjpeg-turbo8-dev libpng-dev libzip-dev \
    curl unzip nano software-properties-common

# Instala Node.js y npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# Instala y configura MySQL correctamente
RUN apt-get install -y mysql-server && \
    mkdir -p /var/run/mysqld && \
    mkdir -p /var/lib/mysql && \
    chown -R mysql:mysql /var/run/mysqld && \
    chown -R mysql:mysql /var/lib/mysql && \
    echo "[mysqld]" >> /etc/mysql/my.cnf && \
    echo "user = mysql" >> /etc/mysql/my.cnf && \
    echo "bind-address = 0.0.0.0" >> /etc/mysql/my.cnf && \
    echo "skip-host-cache" >> /etc/mysql/my.cnf && \
    echo "skip-name-resolve" >> /etc/mysql/my.cnf && \
    service mysql start && \
    sleep 5 && \
    mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'E5pum452025*.';" && \
    mysql -u root -pE5pum452025*. -e "CREATE DATABASE quality_db;" && \
    mysql -u root -pE5pum452025*. -e "CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'E5pum452025*.';" && \
    mysql -u root -pE5pum452025*. -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;" && \
    mysql -u root -pE5pum452025*. -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;" && \
    mysql -u root -pE5pum452025*. -e "FLUSH PRIVILEGES;"

# Agrega el repositorio de PHP 8.2 y lo instala junto con las extensiones requeridas
RUN add-apt-repository ppa:ondrej/php -y && \
    apt-get update && \
    apt-get install -y php8.2 php8.2-fpm php8.2-cli php8.2-common \
    php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath \
    php8.2-intl php8.2-readline php8.2-pcov php8.2-dev

# Instala Swoole desde PECL
RUN pecl install swoole && \
    echo "extension=swoole.so" > /etc/php/8.2/mods-available/swoole.ini && \
    phpenmod swoole

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establece el directorio de trabajo
WORKDIR /app

# Copia los archivos de la aplicación al contenedor
COPY . .

# Configura el archivo .env
RUN echo "APP_NAME=Qualy\n\
APP_ENV=production\n\
APP_KEY=base64:Ob/xPP7Jdm3tJen53VDWZpMUbnvYr+37AJPSYSnd8mY=\n\
APP_DEBUG=false\n\
APP_TIMEZONE=UTC\n\
APP_URL=http://localhost\n\
APP_LOCALE=en\n\
APP_FALLBACK_LOCALE=en\n\
APP_FAKER_LOCALE=en_US\n\
APP_MAINTENANCE_DRIVER=file\n\
PHP_CLI_SERVER_WORKERS=4\n\
BCRYPT_ROUNDS=12\n\
LOG_CHANNEL=stack\n\
LOG_STACK=single\n\
LOG_DEPRECATIONS_CHANNEL=null\n\
LOG_LEVEL=debug\n\
DB_CONNECTION=mysql\n\
DB_HOST=127.0.0.1\n\
DB_PORT=3306\n\
DB_DATABASE=quality_db\n\
DB_USERNAME=root\n\
DB_PASSWORD=E5pum452025*.\n\
SESSION_DRIVER=database\n\
SESSION_LIFETIME=120\n\
SESSION_ENCRYPT=false\n\
SESSION_PATH=/\n\
SESSION_DOMAIN=null\n\
BROADCAST_CONNECTION=log\n\
FILESYSTEM_DISK=local\n\
QUEUE_CONNECTION=database\n\
CACHE_STORE=database\n\
CACHE_PREFIX=\n\
MEMCACHED_HOST=127.0.0.1\n\
REDIS_CLIENT=phpredis\n\
REDIS_HOST=127.0.0.1\n\
REDIS_PASSWORD=null\n\
REDIS_PORT=6379\n\
MAIL_MAILER=log\n\
MAIL_HOST=127.0.0.1\n\
MAIL_PORT=2525\n\
MAIL_USERNAME=null\n\
MAIL_PASSWORD=null\n\
MAIL_ENCRYPTION=null\n\
MAIL_FROM_ADDRESS=\"hello@example.com\"\n\
MAIL_FROM_NAME=\"\${APP_NAME}\"\n\
AWS_ACCESS_KEY_ID=\n\
AWS_SECRET_ACCESS_KEY=\n\
AWS_DEFAULT_REGION=us-east-1\n\
AWS_BUCKET=\n\
AWS_USE_PATH_STYLE_ENDPOINT=false\n\
VITE_APP_NAME=\"\${APP_NAME}\"\n\
OCTANE_SERVER=swoole" > .env

# Instala dependencias de PHP y optimiza Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction && \
    composer dump-autoload --optimize

# Genera la clave de la aplicación si no existe
RUN php artisan key:generate --force

# Configura los permisos
RUN chown -R www-data:www-data /app && \
    chmod -R 775 storage bootstrap/cache

# Instala Laravel Octane
RUN composer require laravel/octane --no-interaction

# Instala Octane con Swoole
RUN php artisan octane:install --server=swoole

# Instala dependencias de Node.js y construye assets para producción
RUN npm ci && \
    npm run build && \
    npm cache clean --force

# Optimiza Laravel para producción
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan event:cache && \
    php artisan icons:cache

# Crea un script de inicio robusto
RUN echo '#!/bin/bash\n\
# Asegura los directorios de MySQL\n\
mkdir -p /var/run/mysqld\n\
mkdir -p /var/lib/mysql\n\
chown -R mysql:mysql /var/run/mysqld\n\
chown -R mysql:mysql /var/lib/mysql\n\
\n\
# Inicia MySQL con manejo de errores\n\
service mysql start\n\
status=$?\n\
if [ $status -ne 0 ]; then\n\
    echo "Failed to start MySQL: $status"\n\
    exit $status\n\
fi\n\
\n\
# Espera a que MySQL esté realmente disponible\n\
echo "Waiting for MySQL to be ready..."\n\
for i in {1..30}; do\n\
    if mysqladmin ping -h"localhost" -u"root" -p"E5pum452025*." --silent; then\n\
        break\n\
    fi\n\
    echo "Waiting for MySQL to be ready... $i/30"\n\
    sleep 1\n\
done\n\
\n\
# Ejecuta las migraciones\n\
echo "Running migrations..."\n\
php artisan migrate --force\n\
\n\
# Limpia y optimiza la aplicación\n\
php artisan optimize:clear\n\
php artisan optimize\n\
\n\
# Inicia Laravel Octane\n\
echo "Starting Laravel Octane..."\n\
php artisan octane:start --server=swoole --host=0.0.0.0 --port=80 --workers=4 --task-workers=2 --max-requests=1000\n\
' > /app/start.sh && chmod +x /app/start.sh

# Comando para iniciar todos los servicios
CMD ["/app/start.sh"]

# Expone el puerto 80
EXPOSE 80