# Guide de déploiement des correctifs

## Bugs corrigés dans cette mise à jour

### 1. Route en doublon (profile.show)
- ❌ **Erreur**: `Unable to prepare route [profile/{user}] for serialization`
- ✅ **Solution**: Renommé `profile.show` en `user.profile` pour éviter le conflit avec Jetstream

### 2. Mode cuisine - page blanche
- ❌ **Erreur**: `TypeError: Cannot read properties of undefined (reading '_s')`
- ✅ **Solution**: Ajout de vérifications de sécurité pour `recipe.steps`

### 3. Garde-manger - recherche d'ingrédients
- ❌ **Problème**: Recherche ne retournait aucun résultat
- ✅ **Solution**: Fix compatibilité PostgreSQL pour la recherche FULLTEXT

### 4. Abonnement - TypeError
- ❌ **Erreur**: `Argument #2 ($prices) must be of type array|string, null given`
- ✅ **Solution**: Validation du `$priceId` avant l'appel à `newSubscription()`

### 5. Planning - drag and drop
- ❌ **Problème**: Impossible d'ajouter des recettes au planning par glisser-déposer
- ✅ **Solution**: Correction complète de la gestion des événements drag/drop

---

## 🚀 Déploiement sur le serveur

### Étape 1 : Connexion au serveur

```bash
ssh ubuntu@51.178.47.162
cd /home/ubuntu/recettes-zero-gaspi
```

### Étape 2 : Pull des dernières modifications

```bash
git fetch origin
git checkout claude/fix-multiple-bugs-01ApvnbzD7CgYdcC1j6GcCri
git pull
```

### Étape 3 : Redémarrer l'application

```bash
./deploy-docker.sh
```

**OU** manuellement :

```bash
# Arrêter les conteneurs
docker compose down

# Rebuild et redémarrer
docker compose up -d --build

# Attendre que les services démarrent (30 secondes)
sleep 30

# Installer les dépendances PHP
docker compose exec laravel.test composer install --no-dev --optimize-autoloader

# Appliquer les migrations
docker compose exec laravel.test php artisan migrate --force

# Clear et rebuild des caches
docker compose exec laravel.test php artisan config:cache
docker compose exec laravel.test php artisan route:cache
docker compose exec laravel.test php artisan view:cache

# Recompiler les assets
docker compose exec laravel.test npm run build
```

### Étape 4 : Vérification

```bash
# Vérifier que les conteneurs tournent
docker compose ps

# Vérifier les logs
docker compose logs -f laravel.test
```

---

## 🧪 Tests à effectuer

### ✅ Garde-manger
1. Accéder à `/pantry`
2. Cliquer sur "Ajouter un article"
3. Taper au moins 2 caractères (ex: "tomate")
4. Vérifier que des résultats s'affichent
5. Sélectionner un ingrédient et ajouter

### ✅ Abonnement
1. Accéder à `/subscription`
2. Cliquer sur "S'abonner"
3. Vérifier le message d'erreur clair (si Stripe non configuré)

### ✅ Planning
1. Accéder à `/meal-plans`
2. Glisser une recette depuis la liste
3. Déposer dans une case du planning
4. Vérifier que la recette s'ajoute

### ✅ Mode cuisine
1. Accéder à une recette avec des étapes
2. Cliquer sur "Mode cuisine"
3. Vérifier que la page se charge correctement
4. Naviguer entre les étapes

### ✅ Profils utilisateurs
1. Cliquer sur un nom d'utilisateur
2. Vérifier que le profil s'affiche
3. Vérifier les notifications de followers

---

## 📝 Configuration Stripe (Optionnel)

Si vous souhaitez activer les abonnements, ajoutez dans `/home/ubuntu/recettes-zero-gaspi/.env` :

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_PRICE_MONTHLY=price_...
STRIPE_PRICE_YEARLY=price_...
```

Puis redémarrez :

```bash
docker compose restart laravel.test
```

---

## 🔍 Dépannage

### Problème : Les conteneurs ne démarrent pas

```bash
docker compose logs laravel.test
docker compose logs mysql
```

### Problème : Erreur 500 sur le site

```bash
# Vérifier les logs Laravel
docker compose exec laravel.test tail -f storage/logs/laravel.log

# Vérifier les permissions
docker compose exec laravel.test chown -R www-data:www-data storage bootstrap/cache
docker compose exec laravel.test chmod -R 775 storage bootstrap/cache
```

### Problème : Assets non compilés

```bash
docker compose exec laravel.test npm run build
```

### Problème : Cache Laravel

```bash
docker compose exec laravel.test php artisan cache:clear
docker compose exec laravel.test php artisan config:clear
docker compose exec laravel.test php artisan route:clear
docker compose exec laravel.test php artisan view:clear
```

---

## 📊 Résumé des fichiers modifiés

### Backend (PHP)
- `app/Http/Controllers/SubscriptionController.php` - Validation Stripe
- `app/Services/IngredientService.php` - Recherche PostgreSQL
- `app/Notifications/FollowerNotification.php` - Routes mises à jour
- `app/Notifications/NewFollowerNotification.php` - Routes mises à jour
- `routes/web.php` - Renommage des routes profile

### Frontend (Vue.js)
- `resources/js/Pages/MealPlan/Index.vue` - Fix drag/drop
- `resources/js/Components/MealPlan/MealPlanGrid.vue` - Fix drag/drop
- `resources/js/Components/MealPlan/RecipeDraggableList.vue` - Fix drag/drop
- `resources/js/Components/MealPlanMobileCard.vue` - Fix drag/drop
- `resources/js/Pages/Recipe/Cook.vue` - Protection undefined
- `resources/js/Layouts/AppLayout.vue` - Routes mises à jour
- `resources/js/Pages/Auth/VerifyEmail.vue` - Routes mises à jour

### Scripts
- `fix-database.sh` - Script de vérification DB
- `BUGFIXES.md` - Documentation complète

---

## ✅ Checklist de déploiement

- [ ] Git pull effectué
- [ ] Conteneurs redémarrés
- [ ] Migrations appliquées
- [ ] Cache cleared
- [ ] Assets recompilés
- [ ] Tests garde-manger OK
- [ ] Tests planning OK
- [ ] Tests mode cuisine OK
- [ ] Tests profils OK
- [ ] Site accessible sur http://51.178.47.162

---

**Date de mise à jour** : 2025-11-14
**Branche** : `claude/fix-multiple-bugs-01ApvnbzD7CgYdcC1j6GcCri`
