# Rapport d'Analyse des Bugs - Production

**Date:** 2025-11-14
**Environnement:** Production OVH (51.178.47.162)
**Status:** 7 bugs identifiés, 2 corrigés, 5 nécessitent action

---

## 🔴 BUGS CRITIQUES (Action immédiate)

### 1. **IMAGES POINTENT VERS LOCALHOST** (CRITIQUE)

**Symptôme:**
```
Access to image at 'http://localhost/storage/1/xxx.png'
from origin 'http://51.178.47.162' has been blocked by CORS policy
```

**Cause:** Configuration `APP_URL=http://localhost` dans .env production

**Impact:**
- ❌ Aucune image de recette ne s'affiche
- ❌ Upload d'images impossible
- ❌ Cooksnaps cassés

**Solution:**
```bash
# Sur le serveur de production, modifier .env :
APP_URL=http://51.178.47.162
# ou avec nom de domaine si disponible :
APP_URL=https://votre-domaine.fr

# Puis redémarrer :
php artisan config:cache
php artisan storage:link
```

**Status:** ⚠️ **CONFIGURATION SERVEUR REQUISE**

---

### 2. **UNITÉS MANQUANTES DANS LA BASE** (CRITIQUE) ✅ CORRIGÉ

**Symptôme:**
- Aucune unité disponible dans garde-manger
- Formulaires recettes sans unités

**Cause:**
1. Colonnes incorrectes dans `Unit::select()` (cherchait `id, name, abbreviation`)
2. Table units a seulement `code` (PK) et `label`
3. Seeder UnitSeeder pas appelé dans DatabaseSeeder

**Corrections appliquées:**
```php
// AVANT (3 fichiers)
Unit::select('id', 'name', 'abbreviation')->get() // ❌ Colonnes inexistantes

// APRÈS
Unit::all() // ✅ Charge code + label (14 lignes seulement)
```

**Fichiers modifiés:**
- `app/Http/Controllers/RecipeController.php` (lignes 58, 150)
- `app/Http/Controllers/PantryController.php` (ligne 62)

**Solution base de données:**
```bash
php artisan db:seed --class=UnitSeeder
# ou
php artisan migrate:fresh --seed
```

**Status:** ✅ **CORRIGÉ CÔTÉ CODE** | ⚠️ **SEED DB REQUIS**

---

## 🟠 BUGS IMPORTANTS

### 3. **MODE CUISINE ERREUR JS**

**Symptôme:**
```javascript
TypeError: Cannot read properties of undefined (reading '_s')
at setup (Cook-CLCimBik.js:5:9620)
```

**Analyse:**
- Code du store Pinia correct ✅
- Controller charge bien `recipe->load(['steps'])` ✅
- Template Cook.vue correct ✅

**Cause probable:**
- Sérialisation Inertia des relations Laravel
- `recipe.steps` peut être `null` ou `undefined` lors du premier rendu
- Store Pinia accède à `recipe.value.steps` avant initialisation

**Solution temporaire:**
Vérifier que les steps sont bien chargés dans le controller :
```php
// RecipeController.php ligne 131-138
$recipe->load(['steps' => function ($query) {
    $query->orderBy('position');
}]);

if ($recipe->steps->isEmpty()) {
    return redirect()->route('recipes.show', $recipe->slug)
        ->with('error', 'Cette recette n\'a pas d\'étapes définies.');
}
```

**Solution définitive:**
Ajouter guards dans Cook.vue :
```vue
<template>
  <div v-if="recipe && recipe.steps && recipe.steps.length > 0">
    <!-- Contenu -->
  </div>
  <div v-else>
    <p>Chargement...</p>
  </div>
</template>
```

**Status:** ⚠️ **NÉCESSITE VERIFICATION** (code semble OK, possiblement problème data)

---

### 4. **DRAG & DROP PLANNING - UX FAIBLE**

**Symptôme:**
- Drag & drop ne fonctionne pas (aucun feedback)
- Pas de changement visuel pendant le drag
- Zones de drop invisibles

**Analyse:**
- Code backend fonctionne ✅
- HTML5 Drag & Drop API native utilisée ✅
- POST vers serveur OK ✅
- **MAIS : Aucun feedback visuel** ❌

**Code manquant:**
```vue
<!-- MealPlanGrid.vue - Ajouter feedback visuel -->
<td
  @dragover="onDragOver"
  @dragenter="onDragEnter(day, mealType)"
  @dragleave="onDragLeave"
  @drop="onDrop(day, mealType)"
  :class="{
    'bg-blue-50 border-blue-400 border-2': isDragOver(day, mealType),
    'bg-white': !isDragOver(day, mealType)
  }"
>
```

**Status:** ⚠️ **FONCTIONNE MAIS UX MAUVAISE**

---

### 5. **STRIPE CHECKOUT CORS ERROR**

**Symptôme:**
```
Access to 'https://checkout.stripe.com/...' has been blocked by CORS policy
```

**Cause:**
- Requête préflight OPTIONS vers Stripe depuis `http://` (non-HTTPS)
- Stripe requiert HTTPS en production

**Solution:**
1. Installer certificat SSL (Let's Encrypt)
2. Configurer HTTPS sur le serveur
3. Mettre à jour APP_URL vers https://

**Status:** ⚠️ **HTTPS REQUIS POUR STRIPE**

---

## 🟡 BUGS MINEURS

### 6. **BOUTON COOKSNAP PAS COHÉRENT**

**Symptôme:**
- Le bouton cooksnap n'utilise pas les composants communs
- Style inconsistant avec le reste de l'app

**Solution:**
Utiliser `<PrimaryButton>` ou `<SecondaryButton>` de `/Components/Common/`

**Status:** ⏳ **Cosmétique, priorité basse**

---

### 7. **RECHERCHE PRODUITS LENTE**

**Symptôme:**
- Recherche de produits trop lente

**Analyse:**
- Probablement requête SQL non optimisée
- Manque d'indexes sur colonnes de recherche

**Solution recommandée:**
```sql
ALTER TABLE ingredients ADD FULLTEXT INDEX ingredients_search (name, brands);
-- ou
ALTER TABLE ingredients ADD INDEX idx_name (name);
```

**Status:** ⏳ **Performance, optimisation future**

---

## 📋 RÉSUMÉ ACTIONS REQUISES

### CÔTÉ CODE (déjà corrigé)
- ✅ Unit::select() corrigé (3 fichiers)
- ✅ Syntaxe PHP validée

### CÔTÉ SERVEUR PRODUCTION (À FAIRE)

#### 1. Configuration .env
```bash
APP_URL=http://51.178.47.162  # ou https:// avec SSL
```

#### 2. Base de données
```bash
php artisan db:seed --class=UnitSeeder
```

#### 3. Cache
```bash
php artisan config:cache
php artisan route:cache
php artisan storage:link
```

#### 4. SSL/HTTPS (pour Stripe)
```bash
# Installer certbot
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d votre-domaine.fr
```

---

## 🎯 PRIORITÉS

| Priorité | Bug | Temps estimé | Bloquant? |
|----------|-----|--------------|-----------|
| 1 | APP_URL localhost | 5 min | ✅ OUI |
| 2 | Unités DB seed | 2 min | ✅ OUI |
| 3 | Mode cuisine JS | 30 min | 🟠 Moyen |
| 4 | Drag & drop UX | 1h | 🟡 Non |
| 5 | Stripe HTTPS | 2h | 🟠 Moyen |
| 6 | Cooksnap button | 15 min | 🟡 Non |
| 7 | Recherche lente | 30 min | 🟡 Non |

---

## 📝 CHECKLIST POST-DÉPLOIEMENT

```bash
# Sur le serveur de production :

# 1. Configurer .env
nano .env
# Modifier APP_URL=http://51.178.47.162

# 2. Seeder units
php artisan db:seed --class=UnitSeeder

# 3. Clear caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Vérifier storage link
php artisan storage:link

# 5. Permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 6. Redémarrer services
sudo systemctl restart php8.4-fpm
sudo systemctl reload nginx
```

---

## 🔧 TESTS À EFFECTUER

Après corrections :
1. ✅ Affichage images recettes
2. ✅ Upload image recette
3. ✅ Formulaire garde-manger (unités visibles)
4. ✅ Formulaire création recette (unités visibles)
5. 🟠 Mode cuisine pas-à-pas
6. 🟠 Drag & drop planning
7. 🟡 Scan code-barres
8. 🟡 Paiement Stripe (nécessite HTTPS)

---

**Conclusion:**
- **2 bugs critiques corrigés côté code** ✅
- **Configuration serveur nécessaire** pour résoudre les autres
- **HTTPS requis** pour fonctionnalités payantes

---

**Prochain commit:** Fixes bugs + documentation configuration production
