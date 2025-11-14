#!/bin/bash

set -e

echo ""
echo "=========================================="
echo "🚀 DÉPLOIEMENT COMPLET - Recettes Zéro Gaspi"
echo "=========================================="
echo ""

cd "$(dirname "$0")"

# ============================================
# 1. NETTOYAGE COMPLET
# ============================================
echo "🧹 1/12 Nettoyage complet..."

# Stop et supprime tous les containers
docker compose down -v 2>/dev/null || true

# Nettoie Docker
docker system prune -f || true

# Supprime vendor et node_modules pour repartir de zéro
echo "   → Suppression vendor et node_modules..."
rm -rf vendor node_modules package-lock.json

# Nettoie les caches Laravel
rm -rf bootstrap/cache/*.php
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*

echo "   ✓ Nettoyage terminé"

# ============================================
# 2. VÉRIFICATION .ENV
# ============================================
echo ""
echo "📝 2/12 Vérification .env..."

if [ ! -f .env ]; then
    echo "   ⚠️  Fichier .env manquant, copie depuis .env.example..."
    cp .env.example .env
    echo "   ⚠️  ATTENTION: Configurez votre .env avant de continuer !"
    echo "   Appuyez sur Entrée quand c'est fait..."
    read
fi

# Vérifications importantes
if ! grep -q "APP_KEY=base64:" .env; then
    echo "   ⚠️  APP_KEY manquant - sera généré après installation Composer"
fi

if ! grep -q "CACHE_STORE=redis" .env; then
    echo "   ⚠️  ATTENTION: Redis devrait être configuré (CACHE_STORE=redis)"
fi

echo "   ✓ .env vérifié"

# ============================================
# 3. INSTALLATION COMPOSER
# ============================================
echo ""
echo "📦 3/12 Installation Composer..."

docker run --rm \
    -v $(pwd):/app \
    -w /app \
    --user $(id -u):$(id -g) \
    composer:latest \
    install --optimize-autoloader --no-cache --ignore-platform-reqs 2>&1 | grep -v "Class \"Redis\" not found" || {
    echo "   ℹ️  Installation terminée (erreurs Redis ignorées)"
}

echo "   ✓ Dépendances Composer installées"

# ============================================
# 4. VÉRIFICATION LARAVEL SAIL
# ============================================
echo ""
echo "✅ 4/12 Vérification Laravel Sail..."

if [ ! -d "vendor/laravel/sail/runtimes/8.4" ]; then
    echo "   ❌ ERREUR: Laravel Sail runtime 8.4 introuvable"
    echo "   Le dossier vendor/laravel/sail/runtimes/8.4 n'existe pas"
    exit 1
fi

echo "   ✓ Laravel Sail runtime 8.4 trouvé"

# ============================================
# 5. BUILD DOCKER
# ============================================
echo ""
echo "🔨 5/12 Build des images Docker..."

export WWWGROUP=$(id -g)
export WWWUSER=$(id -u)

docker compose build --no-cache

echo "   ✓ Images Docker construites"

# ============================================
# 6. DÉMARRAGE CONTAINERS
# ============================================
echo ""
echo "🚀 6/12 Démarrage des containers..."

docker compose up -d

echo "   ✓ Containers démarrés"

# ============================================
# 7. ATTENTE SERVICES
# ============================================
echo ""
echo "⏳ 7/12 Attente du démarrage des services (60s)..."
sleep 60

# Vérification que MySQL est prêt
echo "   → Vérification MySQL..."
for i in {1..30}; do
    if docker compose exec -T mysql mysqladmin ping -h localhost -u sail -ppassword --silent 2>/dev/null; then
        echo "   ✓ MySQL prêt"
        break
    fi
    if [ $i -eq 30 ]; then
        echo "   ⚠️  MySQL met du temps à démarrer, mais on continue..."
    fi
    sleep 2
done

# Vérification que Redis est prêt
echo "   → Vérification Redis..."
if docker compose exec -T redis redis-cli ping 2>/dev/null | grep -q PONG; then
    echo "   ✓ Redis prêt"
else
    echo "   ⚠️  Redis non disponible, mais on continue..."
fi

# ============================================
# 8. GÉNÉRATION APP_KEY SI NÉCESSAIRE
# ============================================
echo ""
echo "🔑 8/12 Génération APP_KEY..."

if ! grep -q "APP_KEY=base64:" .env; then
    docker compose exec -T laravel.test php artisan key:generate
    echo "   ✓ APP_KEY générée"
else
    echo "   ✓ APP_KEY déjà présente"
fi

# ============================================
# 9. NPM INSTALL & BUILD
# ============================================
echo ""
echo "📦 9/12 Installation NPM et build des assets..."

docker compose exec -T laravel.test bash -c "npm install && npm run build"

echo "   ✓ Assets construits"

# ============================================
# 10. MIGRATIONS & SEEDERS
# ============================================
echo ""
echo "🗄️  10/12 Migrations et seeders..."

docker compose exec -T laravel.test php artisan migrate --force

echo "   ✓ Migrations exécutées"

docker compose exec -T laravel.test php artisan db:seed --force

echo "   ✓ Seeders exécutés"

# ============================================
# 11. CACHE LARAVEL
# ============================================
echo ""
echo "⚡ 11/12 Optimisation et cache..."

docker compose exec -T laravel.test php artisan config:cache
docker compose exec -T laravel.test php artisan route:cache
docker compose exec -T laravel.test php artisan view:cache

echo "   ✓ Cache créé"

# ============================================
# 12. STORAGE LINK & PERMISSIONS
# ============================================
echo ""
echo "🔗 12/12 Configuration finale..."

docker compose exec -T laravel.test php artisan storage:link 2>/dev/null || echo "   ℹ️  Storage link déjà créé"

# Fix permissions
docker compose exec -T laravel.test chown -R sail:sail /var/www/html/storage /var/www/html/bootstrap/cache

echo "   ✓ Permissions configurées"

# ============================================
# FIN
# ============================================
echo ""
echo "=========================================="
echo "✅ DÉPLOIEMENT TERMINÉ !"
echo "=========================================="
echo ""
echo "📋 Informations:"
echo ""
docker compose ps
echo ""
echo "🌐 Application disponible sur:"
APP_URL=$(grep APP_URL .env | cut -d '=' -f2)
APP_PORT=$(grep APP_PORT .env | cut -d '=' -f2)
echo "   → ${APP_URL}"
echo ""
echo "📊 Services disponibles:"
echo "   → Mailpit: http://$(echo $APP_URL | sed 's/http:\/\///'):\$(grep FORWARD_MAILPIT_DASHBOARD_PORT .env | cut -d '=' -f2)"
echo ""
echo "📝 Commandes utiles:"
echo "   → Logs:           docker compose logs -f"
echo "   → Artisan:        docker compose exec laravel.test php artisan"
echo "   → Shell:          docker compose exec laravel.test bash"
echo "   → Arrêter:        docker compose down"
echo "   → Redémarrer:     docker compose restart"
echo ""
