composer install
php artisan migrate
php artisan schedule:work &> schedule.out &

php artisan serve --host=0.0.0.0