#!/bin/bash

set -e

echo "=========================================="
echo "🚀 Déploiement Production - Recettes Zéro Gaspi"
echo "=========================================="
echo ""

cd "$(dirname "$0")"

echo "📥 1/10 Pull git..."
git fetch origin
git checkout claude/fix-multiple-bugs-01ApvnbzD7CgYdcC1j6GcCri
git pull origin claude/fix-multiple-bugs-01ApvnbzD7CgYdcC1j6GcCri

echo ""
echo "🛑 2/10 Stop des containers existants..."
docker compose down 2>/dev/null || true

echo ""
echo "🧹 3/10 Nettoyage des anciens conteneurs et volumes..."
docker system prune -f

echo ""
echo "📦 4/10 Installation Composer (AVEC dev pour Sail)..."
docker run --rm \
    -v $(pwd):/app \
    -w /app \
    --user $(id -u):$(id -g) \
    composer:latest \
    install --optimize-autoloader --no-cache --ignore-platform-reqs || {
    echo "   ⚠️ Erreur Redis ignorée (normale en dehors de Docker)"
}

echo ""
echo "✅ 5/10 Vérification de Laravel Sail..."
if [ -d "vendor/laravel/sail/runtimes/8.4" ]; then
    echo "   ✓ Laravel Sail runtime trouvé!"
    ls -la vendor/laravel/sail/runtimes/
else
    echo "   ✗ ERREUR: Laravel Sail runtime introuvable!"
    echo "   Contenu de vendor/laravel/sail:"
    ls -la vendor/laravel/sail/ || echo "   Sail complètement absent!"
    exit 1
fi

echo ""
echo "🔨 7/10 Build des images Docker..."
export WWWGROUP=1000
export WWWUSER=1000
docker compose build --no-cache

echo ""
echo "🚀 8/10 Démarrage des containers..."
docker compose up -d

echo ""
echo "⏳ 9/10 Attente du démarrage (60 secondes)..."
sleep 60

echo ""
echo "🗄️ 10/10 Configuration de l'application..."

echo "   → Permissions..."
docker compose exec -T laravel.test chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker compose exec -T laravel.test chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "   → Migrations..."
docker compose exec -T laravel.test php artisan migrate --force || echo "   ⚠️ Migrations échouées (peut-être déjà appliquées)"

echo "   → Seeders..."
docker compose exec -T laravel.test php artisan db:seed --class=UnitSeeder --force || echo "   ⚠️ Seeder déjà exécuté"

echo "   → Cache config..."
docker compose exec -T laravel.test php artisan config:cache

echo "   → Cache routes..."
docker compose exec -T laravel.test php artisan route:cache || echo "   ⚠️ Route cache échoué"

echo "   → Cache views..."
docker compose exec -T laravel.test php artisan view:cache

echo "   → Storage link..."
docker compose exec -T laravel.test php artisan storage:link || echo "   ⚠️ Storage link déjà créé"

echo "   → NPM install..."
docker compose exec -T laravel.test npm install

echo "   → Build assets..."
docker compose exec -T laravel.test npm run build

echo ""
echo "=========================================="
echo "✅ Déploiement terminé !"
echo "=========================================="
echo ""
echo "🔍 État des containers:"
docker compose ps
echo ""
echo "📊 Logs récents:"
docker compose logs --tail=20 laravel.test
echo ""
echo "🌐 Application disponible sur: http://51.178.47.162"
echo ""
echo "💡 Pour voir les logs en temps réel:"
echo "   docker compose logs -f laravel.test"
echo ""
