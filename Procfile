web: vendor/bin/heroku-php-apache2 public/
release: php artisan migrate --force && php artisan storage:link && php artisan config:clear && php artisan cache:clear && php artisan view:clear && php artisan route:clear
