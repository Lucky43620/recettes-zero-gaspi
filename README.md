# Recettes Zéro Gaspi

Application web de recettes anti-gaspillage avec planning de repas, liste de courses et garde-manger intelligent.

## Stack Technique

Laravel 12 + Vue 3 + Inertia.js + Tailwind CSS + MySQL + Redis + Meilisearch + Stripe

## Installation & Déploiement

### Déploiement One-Click

```bash
git clone https://github.com/Lucky43620/recettes-zero-gaspi.git
cd recettes-zero-gaspi
git checkout claude/fix-multiple-bugs-01ApvnbzD7CgYdcC1j6GcCri
chmod +x deploy.sh
./deploy.sh
```

Le script `deploy.sh` fait TOUT automatiquement :
- ✅ Nettoyage complet
- ✅ Installation Composer
- ✅ Vérification .env
- ✅ Build Docker
- ✅ Démarrage des services
- ✅ Installation NPM et build des assets
- ✅ Migrations et seeders
- ✅ Cache et optimisations

**Durée**: ~5-10 minutes

### Configuration .env

Le fichier `.env` sera créé automatiquement depuis `.env.example`. Configurez au minimum :

```env
APP_URL=http://votre-ip
APP_ENV=production
APP_DEBUG=false

DB_PASSWORD=un_mot_de_passe_securise

CACHE_STORE=redis
REDIS_CLIENT=phpredis
REDIS_HOST=redis
```

## Services Disponibles

Une fois déployé :

- **Application**: http://votre-ip (port 80)
- **Mailpit**: http://votre-ip:8025
- **Meilisearch**: http://votre-ip:7700

## Mise à Jour

Pour mettre à jour l'application après un `git pull` :

```bash
chmod +x update.sh
./update.sh
```

Le script `update.sh` gère automatiquement :
- 📥 Pull des dernières modifications
- 📦 Mise à jour des dépendances (Composer + NPM)
- 🔨 Rebuild des assets Vite
- 🗄️ Migrations de base de données
- ⚡ Clear et rebuild du cache
- 🔄 Redémarrage des containers

**Durée**: ~2-3 minutes

## Commandes Utiles

```bash
# Voir les logs
docker compose logs -f

# Artisan
docker compose exec laravel.test php artisan [commande]

# Shell
docker compose exec laravel.test bash

# Arrêter
docker compose down

# Redémarrer
docker compose restart
```

## Bugs Corrigés

- ✅ Garde-manger: Recherche d'ingrédients (compatibilité PostgreSQL)
- ✅ Abonnement: TypeError sur checkout Stripe
- ✅ Planning: Drag and drop non fonctionnel
- ✅ Routes: Conflit de noms avec Jetstream
- ✅ Mode cuisine: Page blanche
- ✅ NPM: Conflit de versions vite/plugin-vue
- ✅ Traductions: Clés i18n manquantes (auth.register_button, etc.)
- ✅ Routes: Erreur Ziggy user.profile sans paramètre

## Support

Pour toute question ou problème : [GitHub Issues](https://github.com/Lucky43620/recettes-zero-gaspi/issues)
