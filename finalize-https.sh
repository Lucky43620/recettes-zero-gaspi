#!/bin/bash

set -e

echo "=========================================="
echo "🔧 Finalisation HTTPS"
echo "=========================================="

if [ "$EUID" -ne 0 ]; then
    echo "❌ Ce script doit être exécuté en tant que root"
    echo "Utilisez: sudo ./finalize-https.sh"
    exit 1
fi

PROJECT_DIR="/home/ubuntu/recettes-zero-gaspi"
cd $PROJECT_DIR

echo ""
echo "📝 1/3 Mise à jour de APP_URL dans .env..."

# Backup du .env
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# Mettre à jour APP_URL
if grep -q "^APP_URL=" .env; then
    sed -i 's|^APP_URL=.*|APP_URL=https://recettes-zero-gaspi.com|' .env
    echo "✅ APP_URL mis à jour"
else
    echo "APP_URL=https://recettes-zero-gaspi.com" >> .env
    echo "✅ APP_URL ajouté"
fi

# Mettre à jour SESSION_SECURE_COOKIE
if grep -q "^SESSION_SECURE_COOKIE=" .env; then
    sed -i 's|^SESSION_SECURE_COOKIE=.*|SESSION_SECURE_COOKIE=true|' .env
    echo "✅ SESSION_SECURE_COOKIE mis à jour"
else
    echo "SESSION_SECURE_COOKIE=true" >> .env
    echo "✅ SESSION_SECURE_COOKIE ajouté"
fi

# Mettre à jour APP_ENV si nécessaire
if grep -q "^APP_ENV=local" .env; then
    echo ""
    read -p "Voulez-vous passer en mode production (APP_ENV=production) ? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        sed -i 's|^APP_ENV=.*|APP_ENV=production|' .env
        sed -i 's|^APP_DEBUG=.*|APP_DEBUG=false|' .env
        echo "✅ Passé en mode production"
    fi
fi

echo ""
echo "🔄 2/3 Redémarrage de Docker..."
docker compose down
docker compose up -d

echo ""
echo "⏳ Attente du démarrage de Docker..."
sleep 10

echo ""
echo "🧹 3/3 Nettoyage du cache Laravel..."
docker compose exec laravel.test php artisan config:clear
docker compose exec laravel.test php artisan cache:clear
docker compose exec laravel.test php artisan view:clear
docker compose exec laravel.test php artisan route:clear
docker compose exec laravel.test php artisan config:cache

echo ""
echo "✅ Configuration HTTPS finalisée !"
echo ""
echo "📝 Résumé des modifications:"
echo "   - APP_URL: https://recettes-zero-gaspi.com"
echo "   - SESSION_SECURE_COOKIE: true"
echo "   - Docker redémarré"
echo "   - Cache Laravel nettoyé"
echo ""
echo "🔧 Prochaines étapes:"
echo "   1. Testez votre site: https://recettes-zero-gaspi.com"
echo "   2. Mettez à jour le webhook Stripe:"
echo "      https://recettes-zero-gaspi.com/stripe/webhook"
echo "   3. Mettez à jour la clé secrète webhook dans le panel admin"
echo ""
echo "🔐 Le site charge maintenant tous les assets en HTTPS !"
echo "=========================================="
