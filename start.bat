php -r "touch('database\database.sqlite');"

php -r "copy('.env.example', '.env');"

call composer install

call php artisan migrate:fresh

call php artisan db:seed

call php artisan key:generate

call php artisan storage:link

code .

npm install
npm run dev