# Configuration HTTPS pour Recettes Zéro Gaspi

## Prérequis

- Domaine DNS propagé et pointant vers votre VPS (51.178.47.162)
- Accès root au serveur
- Docker et Docker Compose installés et fonctionnels

## Installation automatique

```bash
# Sur le serveur VPS (en tant que root ou avec sudo)
cd /home/ubuntu/recettes-zero-gaspi
sudo ./setup-https.sh
```

Le script va :
1. ✅ Installer Nginx et Certbot
2. ✅ Configurer Nginx comme reverse proxy
3. ✅ Obtenir un certificat SSL Let's Encrypt
4. ✅ Configurer le renouvellement automatique
5. ✅ Activer la redirection HTTP → HTTPS

## Configuration manuelle (si besoin)

### 1. Installer les dépendances

```bash
sudo apt-get update
sudo apt-get install -y nginx certbot python3-certbot-nginx
```

### 2. Configurer Nginx

```bash
# Copier la configuration
sudo cp nginx/recettes-zero-gaspi.conf /etc/nginx/sites-available/recettes-zero-gaspi.com

# Activer le site
sudo ln -s /etc/nginx/sites-available/recettes-zero-gaspi.com /etc/nginx/sites-enabled/

# Désactiver le site par défaut
sudo rm /etc/nginx/sites-enabled/default

# Tester la configuration
sudo nginx -t

# Redémarrer Nginx
sudo systemctl restart nginx
```

### 3. Obtenir le certificat SSL

```bash
sudo mkdir -p /var/www/certbot
sudo certbot certonly --nginx \
  -d recettes-zero-gaspi.com \
  -d www.recettes-zero-gaspi.com \
  --email admin@recettes-zero-gaspi.com \
  --agree-tos \
  --no-eff-email
```

### 4. Recharger Nginx avec SSL

```bash
sudo nginx -t
sudo systemctl reload nginx
```

## Après l'installation

### 1. Mettre à jour Laravel

Dans `/home/ubuntu/recettes-zero-gaspi/.env` :

```env
APP_URL=https://recettes-zero-gaspi.com
SESSION_SECURE_COOKIE=true
```

Puis :

```bash
./update.sh
```

### 2. Mettre à jour Stripe Webhook

Dans le panel Stripe Dashboard :
- Ancienne URL : `http://51.178.47.162/stripe/webhook`
- Nouvelle URL : `https://recettes-zero-gaspi.com/stripe/webhook`

### 3. Mettre à jour les paramètres admin

Dans Admin → Paramètres → Général :
- Mettre à jour l'URL du site si nécessaire

## Renouvellement automatique

Le certificat SSL expire tous les 90 jours. Certbot configure automatiquement un cron job pour le renouvellement.

### Tester le renouvellement

```bash
sudo certbot renew --dry-run
```

### Forcer le renouvellement (si nécessaire)

```bash
sudo certbot renew --force-renewal
sudo systemctl reload nginx
```

## Vérification

1. **Test HTTPS** : https://recettes-zero-gaspi.com
2. **Test SSL** : https://www.ssllabs.com/ssltest/analyze.html?d=recettes-zero-gaspi.com
3. **Redirection HTTP** : http://recettes-zero-gaspi.com (doit rediriger vers HTTPS)

## Dépannage

### Le site n'est pas accessible en HTTPS

```bash
# Vérifier le statut de Nginx
sudo systemctl status nginx

# Vérifier les logs
sudo tail -f /var/log/nginx/error.log

# Vérifier les certificats
sudo certbot certificates
```

### Erreur "Connection refused"

```bash
# Vérifier que Docker est lancé
docker ps

# Vérifier que le port 80 est exposé
netstat -tlnp | grep :80
```

### Erreur de certificat

```bash
# Renouveler le certificat
sudo certbot renew --force-renewal

# Recharger Nginx
sudo systemctl reload nginx
```

## Structure des fichiers

```
recettes-zero-gaspi/
├── nginx/
│   └── recettes-zero-gaspi.conf    # Configuration Nginx
├── setup-https.sh                   # Script d'installation automatique
└── HTTPS-SETUP.md                   # Ce fichier
```

## Sécurité

- ✅ Certificat SSL Let's Encrypt (gratuit, renouvelable)
- ✅ TLS 1.2 et 1.3 uniquement
- ✅ Ciphers sécurisés
- ✅ HSTS configuré
- ✅ Redirection HTTP → HTTPS automatique
- ✅ Headers de sécurité

## Support

En cas de problème :
1. Vérifier les logs Nginx : `/var/log/nginx/error.log`
2. Vérifier les logs Docker : `docker logs laravel.test`
3. Tester la configuration : `sudo nginx -t`
