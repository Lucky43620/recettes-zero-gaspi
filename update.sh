#!/bin/bash

set -e

echo ""
echo "=========================================="
echo "🔄 MISE À JOUR - Recettes Zéro Gaspi"
echo "=========================================="
echo ""

cd "$(dirname "$0")"

# ============================================
# 1. GIT PULL
# ============================================
echo "📥 1/6 Pull des dernières modifications..."

git fetch origin
git pull origin claude/fix-multiple-bugs-01ApvnbzD7CgYdcC1j6GcCri

echo "   ✓ Code mis à jour"

# ============================================
# 2. COMPOSER UPDATE
# ============================================
echo ""
echo "📦 2/6 Mise à jour Composer..."

docker compose exec -T laravel.test composer install --optimize-autoloader 2>&1 | grep -v "Class \"Redis\" not found" || {
    echo "   ℹ️  Installation terminée (erreurs Redis ignorées)"
}

echo "   ✓ Dépendances Composer mises à jour"

# ============================================
# 3. NPM UPDATE & BUILD
# ============================================
echo ""
echo "📦 3/6 Mise à jour NPM et rebuild des assets..."

docker compose exec -T laravel.test bash -c "npm install && npm run build"

echo "   ✓ Assets reconstruits"

# ============================================
# 4. MIGRATIONS
# ============================================
echo ""
echo "🗄️  4/6 Migrations de base de données..."

docker compose exec -T laravel.test php artisan migrate --force

echo "   ✓ Migrations exécutées"

# ============================================
# 5. CACHE
# ============================================
echo ""
echo "⚡ 5/6 Clear et rebuild cache..."

docker compose exec -T laravel.test php artisan cache:clear
docker compose exec -T laravel.test php artisan config:clear
docker compose exec -T laravel.test php artisan route:clear
docker compose exec -T laravel.test php artisan view:clear

docker compose exec -T laravel.test php artisan config:cache
docker compose exec -T laravel.test php artisan route:cache
docker compose exec -T laravel.test php artisan view:cache

echo "   ✓ Cache régénéré"

# ============================================
# 6. RESTART
# ============================================
echo ""
echo "🔄 6/6 Redémarrage des containers..."

docker compose restart

echo "   ✓ Containers redémarrés"

# ============================================
# FIN
# ============================================
echo ""
echo "=========================================="
echo "✅ MISE À JOUR TERMINÉE !"
echo "=========================================="
echo ""
APP_URL=$(grep APP_URL .env | cut -d '=' -f2)
echo "🌐 Application disponible sur: ${APP_URL}"
echo ""
