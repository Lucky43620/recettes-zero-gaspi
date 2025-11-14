# Guide de Configuration Production

**Application:** Recettes Zéro Gaspi
**Serveur:** OVH 51.178.47.162
**Stack:** Laravel 12 + Vue 3 + Inertia + MySQL + Redis + Mailpit

---

## 🚀 DÉPLOIEMENT INITIAL

### 1. Prérequis Serveur

```bash
# Installer dépendances
sudo apt update
sudo apt install -y nginx mysql-server redis-server php8.4-fpm \
  php8.4-mysql php8.4-redis php8.4-mbstring php8.4-xml \
  php8.4-curl php8.4-zip php8.4-gd php8.4-intl php8.4-bcmath \
  composer nodejs npm git

# Vérifier versions
php -v  # >= 8.2
composer -V
node -v  # >= 18
mysql --version
redis-cli --version
```

---

### 2. Cloner et Configurer

```bash
# Cloner le projet
cd /var/www
git clone https://github.com/Lucky43620/recettes-zero-gaspi.git
cd recettes-zero-gaspi

# Permissions
sudo chown -R www-data:www-data /var/www/recettes-zero-gaspi
sudo chmod -R 775 storage bootstrap/cache
```

---

### 3. Configuration .env

```bash
# Copier .env.example
cp .env.example .env

# Éditer .env
nano .env
```

**Variables CRITIQUES à modifier:**

```env
APP_NAME="Recettes Zéro Gaspi"
APP_ENV=production
APP_KEY=                    # Généré par php artisan key:generate
APP_DEBUG=false             # ⚠️ TOUJOURS false en prod
APP_URL=http://51.178.47.162  # ⚠️ CRITIQUE : IP ou domaine du serveur

# Database (créer avant)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=recettes_zero_gaspi_prod
DB_USERNAME=recettes_user
DB_PASSWORD=MOT_DE_PASSE_FORT_ICI

# Redis (local)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail (Mailpit pour test, SMTP réel pour prod)
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_FROM_ADDRESS="noreply@recettes-zero-gaspi.fr"

# Stripe (si actif)
STRIPE_KEY=pk_live_xxx
STRIPE_SECRET=sk_live_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx

# Session & Cache
SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis
```

**Variables SUPPRIMÉES (non utilisées):**
```env
# ❌ Ces variables ont été supprimées
# SCOUT_DRIVER=meilisearch
# MEILISEARCH_HOST=...
# FORWARD_MINIO_PORT=...
# FORWARD_MEILISEARCH_PORT=...
```

---

### 4. Base de Données

```bash
# Se connecter à MySQL
sudo mysql

# Créer base et user
CREATE DATABASE recettes_zero_gaspi_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'recettes_user'@'localhost' IDENTIFIED BY 'MOT_DE_PASSE_FORT';
GRANT ALL PRIVILEGES ON recettes_zero_gaspi_prod.* TO 'recettes_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Tester connexion
mysql -u recettes_user -p recettes_zero_gaspi_prod
```

---

### 5. Installation Dépendances

```bash
# PHP dependencies
composer install --no-dev --optimize-autoloader

# JavaScript dependencies
npm install
npm run build  # Build production

# Générer clé application
php artisan key:generate

# Créer lien storage
php artisan storage:link

# Migrer base de données
php artisan migrate --force

# ⚠️ IMPORTANT : Seeder les unités
php artisan db:seed --class=UnitSeeder

# (Optionnel) Données de test
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=IngredientSeeder
```

---

### 6. Optimisations Production

```bash
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimize autoloader
composer dump-autoload --optimize

# Clear tous les caches (si problème)
php artisan optimize:clear
```

---

## 🔧 CONFIGURATION NGINX

### Fichier : `/etc/nginx/sites-available/recettes-zero-gaspi`

```nginx
server {
    listen 80;
    server_name 51.178.47.162;  # ou votre-domaine.fr
    root /var/www/recettes-zero-gaspi/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    # Logs
    access_log /var/log/nginx/recettes-access.log;
    error_log /var/log/nginx/recettes-error.log;

    # Max upload size (pour images)
    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache assets statiques
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

**Activer le site:**
```bash
sudo ln -s /etc/nginx/sites-available/recettes-zero-gaspi /etc/nginx/sites-enabled/
sudo nginx -t  # Tester config
sudo systemctl reload nginx
```

---

## 🔒 SSL/HTTPS (Recommandé pour Stripe)

### Option 1: Let's Encrypt (Gratuit)

```bash
# Installer Certbot
sudo apt install certbot python3-certbot-nginx

# Obtenir certificat (nécessite nom de domaine)
sudo certbot --nginx -d votre-domaine.fr -d www.votre-domaine.fr

# Auto-renouvellement
sudo certbot renew --dry-run
```

### Option 2: Certificat manuel

```nginx
# Ajouter dans nginx config
listen 443 ssl http2;
ssl_certificate /path/to/certificate.crt;
ssl_certificate_key /path/to/private.key;

# Redirection HTTP -> HTTPS
server {
    listen 80;
    server_name votre-domaine.fr;
    return 301 https://$server_name$request_uri;
}
```

**⚠️ Après SSL, mettre à jour .env:**
```env
APP_URL=https://votre-domaine.fr
```

---

## 📊 SUPERVISOR (Queue Worker)

### Fichier : `/etc/supervisor/conf.d/recettes-worker.conf`

```ini
[program:recettes-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/recettes-zero-gaspi/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/recettes-zero-gaspi/storage/logs/worker.log
stopwaitsecs=3600
```

**Démarrer:**
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start recettes-worker:*
sudo supervisorctl status
```

---

## 📅 CRON JOBS

```bash
# Éditer crontab
sudo crontab -e -u www-data

# Ajouter:
* * * * * cd /var/www/recettes-zero-gaspi && php artisan schedule:run >> /dev/null 2>&1
```

**Jobs programmés dans l'app:**
- Alertes péremption garde-manger (quotidien)
- Rappels planning repas
- Nettoyage sessions expirées

---

## 🔍 MONITORING & LOGS

### Logs Laravel

```bash
# Logs application
tail -f storage/logs/laravel.log

# Logs worker
tail -f storage/logs/worker.log

# Logs Nginx
tail -f /var/log/nginx/recettes-error.log
tail -f /var/log/nginx/recettes-access.log
```

### Log Rotation

```bash
# /etc/logrotate.d/recettes
/var/www/recettes-zero-gaspi/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

---

## 🛠️ MAINTENANCE

### Mise à jour de l'application

```bash
cd /var/www/recettes-zero-gaspi

# Activer mode maintenance
php artisan down

# Pull dernières modifications
git pull origin main

# Installer dépendances
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Migrer DB
php artisan migrate --force

# Clear & cache
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Redémarrer workers
sudo supervisorctl restart recettes-worker:*

# Désactiver mode maintenance
php artisan up
```

---

## ✅ CHECKLIST POST-INSTALLATION

- [ ] .env configuré avec APP_URL correct
- [ ] Base de données créée et migrée
- [ ] **UnitSeeder exécuté** (CRITIQUE)
- [ ] Storage link créé
- [ ] Permissions 775 sur storage/ et bootstrap/cache/
- [ ] Nginx configuré et reload
- [ ] PHP-FPM redémarré
- [ ] Config Laravel cachée
- [ ] SSL installé (si domaine)
- [ ] Supervisor configuré pour queues
- [ ] Cron jobs configurés
- [ ] Tests manuels effectués :
  - [ ] Affichage images
  - [ ] Upload images
  - [ ] Formulaires avec unités
  - [ ] Recherche recettes
  - [ ] Mode cuisine
  - [ ] Planning repas
  - [ ] Paiement Stripe (si SSL)

---

## 🚨 DÉPANNAGE

### Images ne s'affichent pas
```bash
# Vérifier APP_URL
grep APP_URL .env

# Recréer storage link
php artisan storage:link

# Vérifier permissions
ls -la storage/app/public
```

### Erreur 500
```bash
# Logs
tail -50 storage/logs/laravel.log

# Permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Clear cache
php artisan optimize:clear
```

### Unités manquantes
```bash
php artisan db:seed --class=UnitSeeder
```

### Queue ne fonctionne pas
```bash
# Vérifier supervisor
sudo supervisorctl status
sudo supervisorctl restart recettes-worker:*

# Vérifier Redis
redis-cli ping
```

---

**Dernière mise à jour:** 2025-11-14
**Version Laravel:** 12.0
**PHP:** 8.4
