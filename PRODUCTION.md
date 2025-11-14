# Configuration Production

## Variables d'environnement requises

Assurez-vous que votre fichier `.env` sur le serveur contient ces configurations importantes :

### Redis (REQUIS pour le cache)
```env
CACHE_STORE=redis
REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Base de données
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=recettes_zero_gaspi
DB_USERNAME=sail
DB_PASSWORD=VotreMotDePasseSecurise
```

### Application
```env
APP_NAME="Recettes Zéro Gaspi"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://51.178.47.162
APP_PORT=80
```

### Stripe (optionnel)
```env
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_PRICE_MONTHLY=price_...
STRIPE_PRICE_YEARLY=price_...
```

## Déploiement

```bash
cd /home/ubuntu/recettes-zero-gaspi
./fix-git-and-deploy.sh
```

Ce script va :
- Nettoyer les modifications locales
- Récupérer les dernières mises à jour
- Vérifier la configuration Redis
- Déployer l'application complète
