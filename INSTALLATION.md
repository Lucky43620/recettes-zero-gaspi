# Installation de Recettes Zéro Gaspi

## Prérequis

- Docker Desktop (Windows/Mac) ou Docker + Docker Compose (Linux)
- Git

## Installation rapide avec Docker

### 1. Cloner le projet
```bash
git clone <votre-repo>
cd recettes-zero-gaspi
```

### 2. Configurer l'environnement
Le fichier `.env` est déjà configuré pour Docker.

### 3. Démarrer les containers Docker
```bash
./vendor/bin/sail up -d
```

Si c'est la première fois, cela prendra quelques minutes pour télécharger les images Docker.

### 4. Installer les dépendances (si pas déjà fait)
```bash
./vendor/bin/sail composer install
./vendor/bin/sail npm install --legacy-peer-deps
```

### 5. Exécuter les migrations
```bash
./vendor/bin/sail artisan migrate --seed
```

### 6. Compiler les assets
```bash
./vendor/bin/sail npm run build
```

### 7. Accéder à l'application
- **Application** : http://localhost
- **Mailpit** (emails) : http://localhost:8025
- **Meilisearch** (recherche) : http://localhost:7700
- **MinIO** (stockage) : http://localhost:8900

## Commandes utiles

### Démarrer les containers
```bash
./vendor/bin/sail up -d
```

### Arrêter les containers
```bash
./vendor/bin/sail down
```

### Voir les logs
```bash
./vendor/bin/sail logs
```

### Exécuter des commandes Artisan
```bash
./vendor/bin/sail artisan <commande>
```

### Exécuter les tests
```bash
./vendor/bin/sail test
```

### Compiler les assets en mode watch
```bash
./vendor/bin/sail npm run dev
```

### Accéder au shell du container
```bash
./vendor/bin/sail shell
```

## Alias pour Sail (optionnel)

Pour éviter de taper `./vendor/bin/sail` à chaque fois :

### Windows (PowerShell)
```powershell
Set-Alias sail './vendor/bin/sail'
```

### Linux/Mac
```bash
alias sail='./vendor/bin/sail'
```

Ajoutez cet alias à votre `.bashrc` ou `.zshrc` pour le rendre permanent.

## Configuration Stripe (Premium)

Pour activer les abonnements Premium, ajoutez vos clés Stripe dans `.env` :

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
STRIPE_PRICE_MONTHLY=price_...
STRIPE_PRICE_YEARLY=price_...
```

## Problèmes courants

### Les ports sont déjà utilisés
Si les ports 80, 3306, 6379, etc. sont déjà utilisés, modifiez-les dans `.env` :
```env
APP_PORT=8000
FORWARD_DB_PORT=33060
FORWARD_REDIS_PORT=63790
```

### Permission denied sur Linux
Ajoutez votre utilisateur au groupe docker :
```bash
sudo usermod -aG docker $USER
```
Puis redémarrez votre session.

### Les assets ne se compilent pas
Nettoyez le cache npm et réinstallez :
```bash
./vendor/bin/sail npm cache clean --force
./vendor/bin/sail npm install --legacy-peer-deps
```

## Développement sur Windows

Le projet fonctionne parfaitement sur Windows avec Docker Desktop. Assurez-vous d'avoir :
- Docker Desktop installé et démarré
- WSL 2 activé (recommandé pour les performances)
- Le projet cloné dans WSL 2 si possible (meilleures performances)

## Production

Pour un déploiement en production sur Linux, référez-vous à la documentation Laravel :
- Optimisation des configurations
- Configuration HTTPS
- Mise en cache des routes et configs
- Queue workers pour les jobs
- Surveillance et logs

