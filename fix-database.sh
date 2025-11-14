#!/bin/bash

echo "=========================================="
echo "🔧 Script de correction de la base de données"
echo "=========================================="
echo ""

cd "$(dirname "$0")"

echo "📦 Vérification des migrations..."
docker compose exec app php artisan migrate:status

echo ""
echo "🚀 Application des migrations manquantes..."
docker compose exec app php artisan migrate --force

echo ""
echo "✅ Migrations terminées !"

echo ""
echo "🔍 Vérification des colonnes de la table ingredients..."
docker compose exec db psql -U laravel -d laravel -c "\d+ ingredients"

echo ""
echo "=========================================="
echo "✨ Script terminé !"
echo "=========================================="
