php -r "touch('database/database.sqlite');"

php -r "copy('.env.example', '.env');"

composer install

php artisan migrate:fresh

php artisan db:seed

php artisan key:generate

php artisan storage:link

code .
