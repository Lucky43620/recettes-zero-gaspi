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
echo "📥 1/7 Pull des dernières modifications..."

git fetch origin
git pull origin claude/incomplete-description-01MTTFhy38f8SXHgnckz2QMk

echo "   ✓ Code mis à jour"

# ============================================
# 2. COMPOSER UPDATE
# ============================================
echo ""
echo "📦 2/7 Mise à jour Composer..."

docker compose exec -T laravel.test composer install --optimize-autoloader 2>&1 | grep -v "Class \"Redis\" not found" || {
    echo "   ℹ️  Installation terminée (erreurs Redis ignorées)"
}

echo "   ✓ Dépendances Composer mises à jour"

# ============================================
# 3. NPM UPDATE & BUILD
# ============================================
echo ""
echo "📦 3/7 Mise à jour NPM et rebuild des assets..."

docker compose exec -T laravel.test bash -c "npm install && npm run build"

echo "   ✓ Assets reconstruits"

# ============================================
# 4. MIGRATIONS
# ============================================
echo ""
echo "🗄️  4/7 Migrations de base de données..."

docker compose exec -T laravel.test php artisan migrate --force

echo "   ✓ Migrations exécutées"

# ============================================
# 5. STRIPE CONFIGURATION
# ============================================
echo ""
echo "💳 5/7 Configuration Stripe..."

STRIPE_KEY_SET=$(docker compose exec -T laravel.test grep -c "^STRIPE_KEY=sk_" .env 2>/dev/null || echo "0")

if [ "$STRIPE_KEY_SET" = "0" ]; then
    echo "   ⚠️  Stripe non configuré"
    echo ""
    read -p "   STRIPE_KEY (pk_...): " STRIPE_KEY_INPUT
    read -p "   STRIPE_SECRET (sk_...): " STRIPE_SECRET_INPUT
    read -p "   STRIPE_PRICE_MONTHLY (price_...): " STRIPE_PRICE_MONTHLY_INPUT
    read -p "   STRIPE_PRICE_YEARLY (price_...): " STRIPE_PRICE_YEARLY_INPUT
    echo ""

    docker compose exec -T laravel.test sed -i "s/^STRIPE_KEY=.*/STRIPE_KEY=${STRIPE_KEY_INPUT}/" .env
    docker compose exec -T laravel.test sed -i "s/^STRIPE_SECRET=.*/STRIPE_SECRET=${STRIPE_SECRET_INPUT}/" .env
    docker compose exec -T laravel.test sed -i "s/^STRIPE_PRICE_MONTHLY=.*/STRIPE_PRICE_MONTHLY=${STRIPE_PRICE_MONTHLY_INPUT}/" .env
    docker compose exec -T laravel.test sed -i "s/^STRIPE_PRICE_YEARLY=.*/STRIPE_PRICE_YEARLY=${STRIPE_PRICE_YEARLY_INPUT}/" .env

    echo "   ✓ Stripe configuré"
else
    echo "   ✓ Stripe déjà configuré"
fi

# ============================================
# 6. CACHE
# ============================================
echo ""
echo "⚡ 6/7 Clear et rebuild cache..."

docker compose exec -T laravel.test php artisan cache:clear
docker compose exec -T laravel.test php artisan config:clear
docker compose exec -T laravel.test php artisan route:clear
docker compose exec -T laravel.test php artisan view:clear

docker compose exec -T laravel.test php artisan config:cache
docker compose exec -T laravel.test php artisan route:cache
docker compose exec -T laravel.test php artisan view:cache

echo "   ✓ Cache régénéré"

# ============================================
# 7. RESTART
# ============================================
echo ""
echo "🔄 7/7 Redémarrage des containers..."

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
