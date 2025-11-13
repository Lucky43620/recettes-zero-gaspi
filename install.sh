#!/bin/bash

set -e

echo "╔════════════════════════════════════════╗"
echo "║  Installation Recettes Zéro Gaspi     ║"
echo "╚════════════════════════════════════════╝"
echo ""

if ! command -v docker &> /dev/null; then
    echo "❌ Docker n'est pas installé. Veuillez installer Docker Desktop."
    exit 1
fi

if ! docker info &> /dev/null; then
    echo "❌ Docker n'est pas démarré. Veuillez démarrer Docker Desktop."
    exit 1
fi

echo "✅ Docker est installé et en cours d'exécution"
echo ""

echo "📦 Démarrage des containers Docker..."
./vendor/bin/sail up -d

echo ""
echo "⏳ Attente du démarrage de MySQL..."
sleep 10

echo ""
echo "🗄️  Exécution des migrations..."
./vendor/bin/sail artisan migrate --seed

echo ""
echo "🔨 Compilation des assets..."
./vendor/bin/sail npm run build

echo ""
echo "✅ Installation terminée !"
echo ""
echo "╔════════════════════════════════════════╗"
echo "║  L'application est prête !             ║"
echo "╚════════════════════════════════════════╝"
echo ""
echo "🌐 Application      : http://localhost"
echo "📧 Mailpit (emails) : http://localhost:8025"
echo "🔍 Meilisearch     : http://localhost:7700"
echo "📦 MinIO (storage) : http://localhost:8900"
echo ""
echo "Pour arrêter : ./vendor/bin/sail down"
echo "Pour voir les logs : ./vendor/bin/sail logs"
echo ""
