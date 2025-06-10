web: vendor/bin/heroku-php-apache2 -i config/php/custom_php.ini public/
release: php artisan migrate --force && php artisan storage:link && php artisan optimize
