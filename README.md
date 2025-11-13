# 🍽️ Recettes Zéro Gaspi

Application web communautaire de recettes de cuisine avec focus sur la planification des repas et la réduction du gaspillage alimentaire.

![Status](https://img.shields.io/badge/status-production--ready-green)
![Laravel](https://img.shields.io/badge/Laravel-12-red)
![Vue](https://img.shields.io/badge/Vue.js-3-green)
![License](https://img.shields.io/badge/license-MIT-blue)

## 📋 Description

**Recettes Zéro Gaspi** est une plateforme collaborative qui permet aux utilisateurs de :

- 📖 Découvrir et partager des recettes de cuisine
- 🗓️ Planifier leurs repas sur la semaine
- 🛒 Générer automatiquement des listes de courses
- 🥫 Gérer leur garde-manger et éviter le gaspillage
- 📱 Scanner des codes-barres pour ajouter des produits
- ⏰ Recevoir des alertes avant péremption
- 👥 Suivre d'autres cuisiniers et partager leurs créations
- 🏆 Participer à des concours et gagner des badges
- 💎 Accéder à des fonctionnalités Premium

## ✨ Fonctionnalités Principales

### 🔓 Version Gratuite
- Consultation illimitée de recettes
- Création et partage de recettes
- Système social (follow, likes, commentaires)
- Planning de repas basique
- Liste de courses manuelle
- Garde-manger simple
- Favoris et collections

### 💎 Version Premium (4,99€/mois)
- Scan de codes-barres (OpenFoodFacts)
- Alertes de péremption automatiques
- Suggestions de recettes personnalisées par IA
- Garde-manger avancé
- Statistiques détaillées
- Sans publicité
- Contenus exclusifs

## 🚀 Installation Rapide

### Prérequis
- Docker Desktop (Windows/Mac) ou Docker + Docker Compose (Linux)
- Git

### Installation en 3 commandes

```bash
# 1. Cloner le projet
git clone <votre-repo>
cd recettes-zero-gaspi

# 2. Copier et configurer .env (déjà configuré pour Docker)
# Le fichier .env est déjà prêt !

# 3. Lancer avec Docker
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm run build
```

**🎉 C'est prêt !** Accédez à http://localhost

### Scripts d'installation automatique

#### Linux/Mac
```bash
chmod +x install.sh
./install.sh
```

#### Windows (PowerShell)
```powershell
.\install.ps1
```

Pour plus de détails, consultez [INSTALLATION.md](INSTALLATION.md)

## 📚 Documentation

- **[INSTALLATION.md](INSTALLATION.md)** - Guide d'installation complet
- **[VERIFICATION_COMPLETE.md](VERIFICATION_COMPLETE.md)** - Rapport de vérification technique
- **[STATUS.md](STATUS.md)** - État d'avancement du projet
- **[TESTING.md](TESTING.md)** - Documentation des tests

## 🛠️ Stack Technique

### Backend
- **Laravel 12** - Framework PHP
- **MySQL 8.0** - Base de données
- **Redis** - Cache
- **Meilisearch** - Moteur de recherche
- **MinIO** - Stockage S3-compatible
- **Laravel Cashier** - Paiements Stripe

### Frontend
- **Vue 3** - Framework JavaScript
- **Inertia.js** - SPA sans API
- **Tailwind CSS 4** - Framework CSS
- **Vite 7** - Build tool
- **PWA** - Progressive Web App

### Intégrations
- **Stripe** - Paiements
- **OpenFoodFacts** - Base de données alimentaires
- **Mailpit** - Emails (développement)

## 🌍 Langues Supportées

- 🇫🇷 Français
- 🇬🇧 Anglais
- 🇪🇸 Espagnol
- 🇩🇪 Allemand
- 🇮🇹 Italien

## 🔐 Sécurité

- ✅ CSRF protection
- ✅ Rate limiting
- ✅ XSS protection
- ✅ SQL injection protection
- ✅ RGPD compliant
- ✅ Données chiffrées

## 📊 Tests

```bash
# Lancer les tests
./vendor/bin/sail test

# Tests avec couverture
./vendor/bin/sail test --coverage
```

**169 tests passent** ✅

## 🐳 Services Docker

Une fois lancé, vous avez accès à :

| Service | URL | Description |
|---------|-----|-------------|
| Application | http://localhost | Application principale |
| Mailpit | http://localhost:8025 | Interface emails |
| Meilisearch | http://localhost:7700 | Dashboard recherche |
| MinIO Console | http://localhost:8900 | Console stockage |

## 🎯 Roadmap

- [x] Jalons 0-7 : Fonctionnalités core
- [x] Jalon 8 : Système Premium/Freemium
- [ ] Tests E2E (Playwright)
- [ ] Intégrations drives
- [ ] IA génération de menus
- [ ] Recherche vocale

## 📝 Commandes Utiles

```bash
# Démarrer les containers
./vendor/bin/sail up -d

# Arrêter les containers
./vendor/bin/sail down

# Voir les logs
./vendor/bin/sail logs

# Accéder au shell
./vendor/bin/sail shell

# Exécuter les migrations
./vendor/bin/sail artisan migrate

# Lancer les tests
./vendor/bin/sail test

# Compiler les assets en watch
./vendor/bin/sail npm run dev

# Build production
./vendor/bin/sail npm run build
```

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Fork le projet
2. Créez une branche (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## 📄 License

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 👨‍💻 Auteur

Développé avec ❤️ pour lutter contre le gaspillage alimentaire

## 🙏 Remerciements

- [Laravel](https://laravel.com)
- [Vue.js](https://vuejs.org)
- [Tailwind CSS](https://tailwindcss.com)
- [OpenFoodFacts](https://fr.openfoodfacts.org)
- [Stripe](https://stripe.com)
- La communauté open source

---

**⭐ Si vous aimez ce projet, n'hésitez pas à lui donner une étoile !**
