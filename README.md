# Recettes Zéro Gaspi

Application web de recettes anti-gaspillage avec planning de repas, liste de courses et garde-manger intelligent.

## Installation

```bash
git clone <repo>
cd recettes-zero-gaspi
cp .env.example .env
docker-compose up -d --build
```

Attendre que les containers démarrent, puis :

```bash
docker-compose exec laravel.test php artisan key:generate
docker-compose exec laravel.test php artisan migrate --seed
docker-compose exec laravel.test npm install
docker-compose exec laravel.test npm run build
```

Application : http://localhost

## Commandes

**Start/Stop:**
```bash
docker-compose start
docker-compose stop
```

**Update:**
```bash
docker-compose down
git pull
docker-compose up -d --build
```

**Logs:**
```bash
docker-compose logs -f
```

## Services

- App: http://localhost
- Mailpit: http://localhost:8025
- Meilisearch: http://localhost:7700
- MinIO: http://localhost:8900

## Stack

Laravel 12 + Vue 3 + Inertia + Tailwind + MySQL + Redis + Meilisearch + Stripe
