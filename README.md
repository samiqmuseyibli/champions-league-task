### Deployment
1) Download or Clone repo
2) Run `cp .env.example .env` file to copy example file to .env
3) Then edit your `.env` file with DB credentials and other settings.
4) Run `composer install` command
5) Run `php artisan migrate --seed` command.
6) Run `php artisan key:generate` command.
6) Run `php artisan serve` command to Run project.
