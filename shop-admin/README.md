# 🛒 Shop Admin - Laravel 8
# � Shop Admin (Laravel)

This repository contains a Laravel-based admin panel and simple store management app.

This README explains how to run the project using Docker for development (DB + phpMyAdmin) or using the full Docker Compose stack (nginx + php-fpm + mysql).

Requirements (if running locally)
- Docker & Docker Compose (recommended)
- PHP 8.1 (if running without Docker)
- Composer

Quick start — two recommended flows

1) Full Docker (recommended)

This will run the entire app in containers (PHP-FPM, Nginx, MySQL).

- Create or copy your environment file:

```bash
cp .env.example .env
```

- Update `.env` database settings to match the docker-compose service (if you want to use the container environment values, you can keep these defaults):

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=shop_admin
DB_USERNAME=shop
DB_PASSWORD=secret
```

- Build and start containers:

```bash
docker compose up -d --build
```

- Install PHP dependencies and run migrations/seeds inside the `app` container:

```bash
docker compose exec app bash
composer install --no-interaction --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
exit
```

- Open the app in your browser:

http://localhost

2) Development (use `docker-compose.dev.yml` for DB + phpMyAdmin and run PHP locally)

This flow is useful if you prefer running PHP on your host machine but want a containerized database.

- Start only the DB + phpMyAdmin services:

```bash
docker compose -f docker-compose.dev.yml up -d
```

- Copy `.env` and update DB settings to connect from your host to the MySQL container (note the forwarded port 3307):

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=ShopAdminDB
DB_USERNAME=root
DB_PASSWORD=root
```

- Install dependencies and run the app on your machine:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

- Open the app at:

http://127.0.0.1:8000

- phpMyAdmin is available at:

http://127.0.0.1:8081

Helpful artisan commands

```bash
php artisan view:clear
php artisan route:clear
php artisan config:clear
```

Notes
- The project ships with a `docker-compose.yml` for full-stack containers and a `docker-compose.dev.yml` that provides only MySQL + phpMyAdmin for a host-based PHP development flow.
- If you run the full Docker Compose setup, be sure your `.env` DB_HOST is `db` (the service name). If you run the DB-only dev compose and use `php artisan serve` from the host, point DB_HOST to `127.0.0.1` and DB_PORT to `3307` (the port mapped in `docker-compose.dev.yml`).
- Default seeded users (see DatabaseSeeder):
	- admin@example.com / password (admin)
	- user1@example.com / password (user)
	- user2@example.com / password (user)

If you want, I can add a small `Makefile` or helper scripts to simplify these commands.
