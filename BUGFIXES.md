# Corrections de bugs - Recettes Zéro Gaspi

## Date : 2025-11-14

### Résumé des bugs corrigés

#### 1. 🔍 **Garde-manger : Recherche d'ingrédient ne fonctionne pas**

**Problème :**
- La recherche d'ingrédients dans le modal d'ajout au garde-manger ne retournait pas de résultats
- Le bouton "Ajouter" restait grisé car aucun ingrédient ne pouvait être sélectionné

**Cause :**
- La requête FULLTEXT (`MATCH AGAINST`) dans `IngredientService.php` n'est pas compatible avec PostgreSQL
- PostgreSQL utilise un système de recherche différent de MySQL

**Solution appliquée :**
- Ajout d'une détection du driver de base de données dans `IngredientService::searchLocal()`
- Utilisation de FULLTEXT uniquement pour MySQL
- Fallback sur une recherche avec `LIKE` pour PostgreSQL et en cas d'erreur
- Ajout d'un try-catch pour gérer les erreurs de recherche FULLTEXT

**Fichiers modifiés :**
- `app/Services/IngredientService.php` (lignes 11, 156-170)

---

#### 2. 💳 **Abonnement : Erreur "Argument #2 ($prices) must be of type array|string, null given"**

**Problème :**
- Erreur critique lors de la tentative de souscription à un abonnement
- TypeError dans `SubscriptionController::checkout()` ligne 85

**Cause :**
- Les variables d'environnement `STRIPE_PRICE_MONTHLY` et `STRIPE_PRICE_YEARLY` n'étaient pas configurées
- Le code passait `null` à la méthode `newSubscription()` de Laravel Cashier

**Solution appliquée :**
- Ajout d'une validation pour vérifier que `$priceId` n'est pas vide avant d'appeler `newSubscription()`
- Retour d'un message d'erreur explicite si les prix Stripe ne sont pas configurés

**Fichiers modifiés :**
- `app/Http/Controllers/SubscriptionController.php` (lignes 84-86)

---

#### 3. 📅 **Planning : Drag and drop ne fonctionne pas**

**Problème :**
- Le glisser-déposer des recettes dans le planning ne fonctionnait pas
- Les recettes ne s'ajoutaient pas aux cases du planning

**Cause :**
- Les événements `dragstart`, `dragover` et `drop` n'étaient pas correctement configurés
- L'objet `event` n'était pas passé aux handlers
- Manque de `preventDefault()` dans l'événement `drop`
- Absence de configuration de `dataTransfer.effectAllowed` et `dropEffect`

**Solution appliquée :**
- Modification de `onDragStart()` pour accepter l'événement et configurer `dataTransfer`
- Ajout de `dropEffect = 'copy'` dans `onDragOver()`
- Ajout de `event.preventDefault()` dans `onDrop()`
- Mise à jour de tous les composants pour passer l'événement :
  - `RecipeDraggableList.vue`
  - `MealPlanGrid.vue`
  - `MealPlanMobileCard.vue`

**Fichiers modifiés :**
- `resources/js/Pages/MealPlan/Index.vue` (lignes 70-95)
- `resources/js/Components/MealPlan/RecipeDraggableList.vue` (ligne 47)
- `resources/js/Components/MealPlan/MealPlanGrid.vue` (lignes 49-50)
- `resources/js/Components/MealPlan/MealPlanMobileCard.vue` (lignes 26-27)

---

## Instructions de déploiement

### 1. Mise à jour du code

```bash
cd /home/user/recettes-zero-gaspi
git pull origin claude/fix-multiple-bugs-01ApvnbzD7CgYdcC1j6GcCri
```

### 2. Vérification et application des migrations

```bash
./fix-database.sh
```

ou manuellement :

```bash
docker compose exec app php artisan migrate --force
```

### 3. Configuration des variables d'environnement Stripe (optionnel)

Si vous souhaitez activer les abonnements, ajoutez dans votre `.env` :

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_PRICE_MONTHLY=price_...
STRIPE_PRICE_YEARLY=price_...
```

### 4. Recompilation des assets

```bash
docker compose exec app npm run build
```

### 5. Redémarrage des conteneurs

```bash
docker compose restart
```

---

## Tests à effectuer

### Garde-manger
1. ✅ Ouvrir le modal "Ajouter un article"
2. ✅ Taper au moins 2 caractères dans la recherche (ex: "tomate")
3. ✅ Vérifier que des résultats s'affichent
4. ✅ Sélectionner un ingrédient
5. ✅ Vérifier que le bouton "Ajouter" est maintenant actif
6. ✅ Ajouter l'article au garde-manger

### Abonnement
1. ✅ Accéder à la page `/subscription`
2. ✅ Cliquer sur "S'abonner" pour un plan
3. ✅ Vérifier qu'un message d'erreur clair s'affiche (si Stripe non configuré)
   OU que la redirection vers Stripe fonctionne (si configuré)

### Planning
1. ✅ Accéder à la page `/meal-plans`
2. ✅ Glisser une recette depuis la liste de gauche
3. ✅ Déposer la recette dans une case du planning
4. ✅ Vérifier que la recette s'ajoute bien au planning
5. ✅ Tester sur mobile également

---

## Notes techniques

### Compatibilité base de données

Le code est maintenant compatible avec **PostgreSQL** ET **MySQL**.

La recherche d'ingrédients utilise :
- **MySQL** : Index FULLTEXT avec `MATCH AGAINST` (plus rapide)
- **PostgreSQL** : Recherche avec `LIKE` (compatible mais légèrement plus lent)

### Améliorations futures suggérées

1. **Garde-manger** : Implémenter une recherche PostgreSQL optimisée avec `pg_trgm` (extension trigram)
2. **Abonnement** : Ajouter un message plus clair sur la page d'abonnement si Stripe n'est pas configuré
3. **Planning** : Ajouter un feedback visuel lors du drag (ex: ombre, cursor personnalisé)

---

## Support

En cas de problème, vérifier :
1. Les logs Docker : `docker compose logs -f app`
2. Les logs Laravel : `storage/logs/laravel.log`
3. La console navigateur (F12) pour les erreurs JavaScript
