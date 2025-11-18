#!/bin/bash

set -e

echo "=========================================="
echo "🔒 Configuration HTTPS - Recettes Zéro Gaspi"
echo "=========================================="

if [ "$EUID" -ne 0 ]; then
    echo "❌ Ce script doit être exécuté en tant que root"
    echo "Utilisez: sudo ./setup-https.sh"
    exit 1
fi

DOMAIN="recettes-zero-gaspi.com"
EMAIL="lulu.bruyere43@gmail.com"
PROJECT_DIR="/home/ubuntu/recettes-zero-gaspi"

echo ""
echo "📧 Email pour Let's Encrypt: $EMAIL"
echo "🌐 Domaine: $DOMAIN"
echo "📁 Répertoire projet: $PROJECT_DIR"
echo ""
read -p "Continuer ? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    exit 1
fi

echo ""
echo "🛑 1/8 Arrêt de Docker..."
cd $PROJECT_DIR
docker-compose down || true

echo ""
echo "🔧 2/8 Configuration du port Docker sur 8080..."
if ! grep -q "APP_PORT=8080" .env; then
    echo "APP_PORT=8080" >> .env
    echo "✅ APP_PORT=8080 ajouté au .env"
else
    echo "✅ APP_PORT déjà configuré"
fi

echo ""
echo "🚀 3/8 Redémarrage de Docker sur le port 8080..."
docker-compose up -d
sleep 5

echo ""
echo "✅ Docker redémarré et écoute sur http://127.0.0.1:8080"

echo ""
echo "📦 4/8 Installation de Nginx (si nécessaire)..."
systemctl stop nginx 2>/dev/null || true

echo ""
echo "📋 5/8 Configuration Nginx (HTTP seulement pour obtenir les certificats)..."
cp $PROJECT_DIR/nginx/recettes-zero-gaspi-http-only.conf /etc/nginx/sites-available/recettes-zero-gaspi.com

if [ -L /etc/nginx/sites-enabled/default ]; then
    rm /etc/nginx/sites-enabled/default
fi

if [ -L /etc/nginx/sites-enabled/recettes-zero-gaspi.com ]; then
    rm /etc/nginx/sites-enabled/recettes-zero-gaspi.com
fi

ln -s /etc/nginx/sites-available/recettes-zero-gaspi.com /etc/nginx/sites-enabled/

echo ""
echo "🔍 Test de la configuration Nginx..."
nginx -t

echo ""
echo "🔄 Démarrage de Nginx..."
systemctl start nginx
systemctl enable nginx

echo ""
echo "🔒 6/8 Obtention du certificat SSL..."
mkdir -p /var/www/certbot
certbot certonly --nginx -d $DOMAIN -d www.$DOMAIN --email $EMAIL --agree-tos --no-eff-email --non-interactive

echo ""
echo "📋 7/8 Activation de la configuration HTTPS complète..."
cp $PROJECT_DIR/nginx/recettes-zero-gaspi.conf /etc/nginx/sites-available/recettes-zero-gaspi.com

echo ""
echo "🔍 Test de la configuration Nginx avec SSL..."
nginx -t

echo ""
echo "🔄 8/8 Rechargement final de Nginx avec SSL..."
systemctl reload nginx

echo ""
echo "✅ Configuration HTTPS terminée !"
echo ""
echo "📝 Informations importantes:"
echo "   - Votre site est accessible via: https://$DOMAIN"
echo "   - Docker écoute sur: http://127.0.0.1:8080"
echo "   - Nginx fait le reverse proxy avec SSL"
echo "   - Certificat SSL valide pour 90 jours"
echo "   - Renouvellement automatique configuré"
echo ""
echo "🔧 Prochaines étapes:"
echo "   1. Mettez à jour APP_URL dans .env: APP_URL=https://$DOMAIN"
echo "   2. Mettez à jour l'URL du webhook Stripe"
echo "   3. Testez votre site: https://$DOMAIN"
echo ""
echo "🔄 Pour redémarrer les services:"
echo "   - Docker: cd $PROJECT_DIR && docker-compose restart"
echo "   - Nginx: sudo systemctl restart nginx"
echo ""
echo "=========================================="
