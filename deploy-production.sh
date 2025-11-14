#!/bin/bash

set -e

echo "=========================================="
echo "🚀 Déploiement Production - Recettes Zéro Gaspi"
echo "=========================================="
echo ""

cd "$(dirname "$0")"

echo "📥 1/9 Pull git..."
git fetch origin
git checkout claude/fix-multiple-bugs-01ApvnbzD7CgYdcC1j6GcCri
git pull

echo ""
echo "🛑 2/9 Stop des containers existants..."
docker compose down 2>/dev/null || true

echo ""
echo "📦 3/9 Installation des dépendances Composer..."
if [ ! -d "vendor" ]; then
    echo "   → vendor n'existe pas, installation avec Docker temporaire..."
    docker run --rm -v $(pwd):/app -w /app composer:latest install --no-dev --optimize-autoloader --ignore-platform-reqs
else
    echo "   → vendor existe, mise à jour..."
    docker run --rm -v $(pwd):/app -w /app composer:latest update --no-dev --optimize-autoloader --ignore-platform-reqs
fi

echo ""
echo "🔨 4/9 Build des images Docker..."
docker compose build --no-cache

echo ""
echo "🚀 5/9 Démarrage des containers..."
docker compose up -d

echo ""
echo "⏳ 6/9 Attente des services (40s)..."
sleep 40

echo ""
echo "🗄️ 7/9 Migrations base de données..."
docker compose exec -T laravel.test php artisan migrate --force

echo ""
echo "📝 8/9 Configuration Laravel..."
docker compose exec -T laravel.test php artisan config:cache
docker compose exec -T laravel.test php artisan route:cache
docker compose exec -T laravel.test php artisan view:cache

echo ""
echo "🎨 9/9 Build des assets frontend..."
docker compose exec -T laravel.test npm install
docker compose exec -T laravel.test npm run build

echo ""
echo "=========================================="
echo "✅ Déploiement terminé avec succès !"
echo "=========================================="
echo ""
echo "🔍 Vérifications:"
docker compose ps
echo ""
echo "🌐 Application disponible sur: http://51.178.47.162"
echo ""
