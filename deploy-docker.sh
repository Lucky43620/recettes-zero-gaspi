#!/bin/bash

# Script de déploiement Docker - Recettes Zéro Gaspi
# Usage: ./deploy-docker.sh

set -e

echo "🚀 Déploiement Docker - Recettes Zéro Gaspi"
echo "============================================"

# 1. Pull dernières modifications
echo ""
echo "📥 1/8 Pull git..."
git pull origin main

# 2. Stop containers
echo ""
echo "🛑 2/8 Stop des containers..."
docker-compose down

# 3. Build images
echo ""
echo "🔨 3/8 Build des images Docker..."
docker-compose build --no-cache

# 4. Start containers
echo ""
echo "🚀 4/8 Démarrage des containers..."
docker-compose up -d

# 5. Attendre que les services soient prêts
echo ""
echo "⏳ 5/8 Attente des services (30s)..."
sleep 30

# 6. Installation dépendances et setup Laravel
echo ""
echo "📦 6/8 Installation dépendances..."
docker-compose exec -T laravel.test composer install --no-dev --optimize-autoloader

echo ""
echo "🔧 7/8 Configuration Laravel..."

# Storage link
docker-compose exec -T laravel.test php artisan storage:link

# Migrations (si nécessaire)
docker-compose exec -T laravel.test php artisan migrate --force

# Seed unités (CRITIQUE)
echo "   → Seeding unités..."
docker-compose exec -T laravel.test php artisan db:seed --class=UnitSeeder

# Clear et cache
echo "   → Clear caches..."
docker-compose exec -T laravel.test php artisan config:cache
docker-compose exec -T laravel.test php artisan route:cache
docker-compose exec -T laravel.test php artisan view:cache

# Fix permissions storage
echo "   → Fix permissions storage..."
docker-compose exec -T laravel.test chmod -R 775 storage bootstrap/cache
docker-compose exec -T laravel.test chown -R www-data:www-data storage bootstrap/cache

# 8. Build assets frontend
echo ""
echo "🎨 8/8 Build assets frontend..."
docker-compose exec -T laravel.test npm install
docker-compose exec -T laravel.test npm run build

echo ""
echo "✅ Déploiement terminé !"
echo ""
echo "🔍 Vérifications à faire :"
echo "   - Images : http://votre-serveur/storage/"
echo "   - Unités : Tester formulaire garde-manger"
echo "   - Recettes : Créer/éditer recette"
echo ""
echo "📊 Status containers :"
docker-compose ps
