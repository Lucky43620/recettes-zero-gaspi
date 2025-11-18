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
EMAIL="admin@recettes-zero-gaspi.com"

echo ""
echo "📧 Email pour Let's Encrypt: $EMAIL"
echo "🌐 Domaine: $DOMAIN"
echo ""
read -p "Continuer ? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    exit 1
fi

echo ""
echo "📦 1/5 Installation de Nginx et Certbot..."
apt-get update
apt-get install -y nginx certbot python3-certbot-nginx

echo ""
echo "📋 2/5 Copie de la configuration Nginx..."
cp /home/ubuntu/recettes-zero-gaspi/nginx/recettes-zero-gaspi.conf /etc/nginx/sites-available/recettes-zero-gaspi.com

if [ -L /etc/nginx/sites-enabled/default ]; then
    rm /etc/nginx/sites-enabled/default
fi

if [ ! -L /etc/nginx/sites-enabled/recettes-zero-gaspi.com ]; then
    ln -s /etc/nginx/sites-available/recettes-zero-gaspi.com /etc/nginx/sites-enabled/
fi

echo ""
echo "🔍 3/5 Test de la configuration Nginx..."
nginx -t

echo ""
echo "🔄 4/5 Redémarrage de Nginx..."
systemctl restart nginx
systemctl enable nginx

echo ""
echo "🔒 5/5 Obtention du certificat SSL..."
mkdir -p /var/www/certbot
certbot certonly --nginx -d $DOMAIN -d www.$DOMAIN --email $EMAIL --agree-tos --no-eff-email

echo ""
echo "🔄 Rechargement final de Nginx avec SSL..."
nginx -t && systemctl reload nginx

echo ""
echo "✅ Configuration HTTPS terminée !"
echo ""
echo "📝 Informations importantes:"
echo "   - Votre site est maintenant accessible via: https://$DOMAIN"
echo "   - Certificat SSL valide pour 90 jours"
echo "   - Renouvellement automatique configuré"
echo ""
echo "🔧 Prochaines étapes:"
echo "   1. Mettez à jour APP_URL dans .env: APP_URL=https://$DOMAIN"
echo "   2. Mettez à jour l'URL du webhook Stripe"
echo "   3. Testez votre site: https://$DOMAIN"
echo ""
echo "==========================================
"
