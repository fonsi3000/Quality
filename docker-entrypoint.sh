#!/bin/bash
set -e

echo "Waiting for database connection..."
while ! nc -z db 3306; do
  sleep 1
done

php artisan migrate --seed --force
php artisan octane:start --server=swoole --host=0.0.0.0 --port=80 --workers=4 --task-workers=2 --max-requests=1000