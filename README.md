# Chibog
Repository for chibog


## Setup:

1. Run `composer install`
2. Run `npm install`
3. Run `php artisan migrate`
4. Run `php artisan db:seed`

After you run `php artisan db:seed`, it will create a user:

Your default email/password for web & vendor is: **admin@admin.com / admin**

## Start your laravel app:

1. Run `php artisan serve`
2. Run `php artisan queue:listen --queue=emails` to send an email for ordering/email verification
3. Run `npm run watch`
