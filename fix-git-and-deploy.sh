#!/bin/bash

set -e

echo "=========================================="
echo "🔧 Fix Git + Déploiement"
echo "=========================================="
echo ""

cd "$(dirname "$0")"

echo "🧹 Nettoyage des modifications locales..."
git reset --hard HEAD
git clean -fd

echo ""
echo "📥 Pull des dernières modifications..."
git fetch origin
git checkout claude/fix-multiple-bugs-01ApvnbzD7CgYdcC1j6GcCri
git pull origin claude/fix-multiple-bugs-01ApvnbzD7CgYdcC1j6GcCri

echo ""
echo "✅ Git à jour !"
echo ""
echo "🚀 Lancement du déploiement..."
chmod +x deploy-fix.sh
./deploy-fix.sh
