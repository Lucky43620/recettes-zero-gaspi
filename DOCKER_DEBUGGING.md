# Guide de Débogage - Docker

**Dernière mise à jour:** 2025-11-14

---

## 🔴 PROBLÈME: 403 Forbidden sur /storage/

### Symptôme
```
GET http://51.178.47.162/storage/1/xxx.png 403 (Forbidden)
```

### Causes possibles

1. **Lien symbolique manquant**
2. **Permissions incorrectes**
3. **Nginx mal configuré dans Docker**

---

### Solution 1: Recréer le lien symbolique

```bash
# Dans le container
docker-compose exec laravel.test php artisan storage:link

# Vérifier le lien
docker-compose exec laravel.test ls -la public/storage
# Devrait afficher : public/storage -> ../storage/app/public
```

---

### Solution 2: Fix permissions

```bash
# Dans le container
docker-compose exec laravel.test chmod -R 775 storage
docker-compose exec laravel.test chown -R www-data:www-data storage
```

---

### Solution 3: Vérifier structure

```bash
# Vérifier que les fichiers existent
docker-compose exec laravel.test ls -la storage/app/public/1/
docker-compose exec laravel.test ls -la storage/app/public/2/

# Vérifier contenu des dossiers media
docker-compose exec laravel.test find storage/app/public -type f | head -20
```

---

## 🟡 PROBLÈME: Unités dropdown vide/blanc

### Symptôme
Dropdown affiche "Sélectionné" mais le reste est blanc.

### Cause
Templates Vue cherchent `unit.name` mais model Unit retourne `unit.label`.

### Solution ✅ CORRIGÉE
Ajout d'un accessor `name` dans `app/Models/Unit.php` :

```php
protected $appends = ['name'];

public function getNameAttribute(): string
{
    return $this->label;
}
```

**Après fix:** Les unités s'affichent correctement dans tous les dropdowns.

---

## 🟠 PROBLÈME: Drag & Drop ne fonctionne pas

### Symptôme
Impossible de drag & drop une recette dans le planning.

### Diagnostic

Le drag & drop **fonctionne techniquement** mais manque de feedback visuel :
- Pas de changement de couleur pendant le drag
- Pas d'indication des zones de drop
- Pas de curseur grab/grabbing

### Fichiers concernés
- `resources/js/Components/MealPlan/MealPlanGrid.vue`
- `resources/js/Components/MealPlan/RecipeDraggableList.vue`
- `resources/js/Pages/MealPlan/Index.vue`

### Amélioration future (1-2h)

Ajouter feedback visuel dans `MealPlanGrid.vue` :

```vue
<template>
  <td
    @dragover.prevent="onDragOver"
    @dragenter="setDragTarget(day, mealType)"
    @dragleave="clearDragTarget"
    @drop="onDrop(day, mealType)"
    :class="{
      'bg-blue-50 border-blue-400 border-2': isDragTarget(day, mealType),
      'hover:bg-gray-50': !isDragTarget(day, mealType)
    }"
  >
    <!-- contenu -->
  </td>
</template>

<script setup>
const dragTarget = ref(null);

const isDragTarget = (day, mealType) => {
  return dragTarget.value?.day === day && dragTarget.value?.mealType === mealType;
};

const setDragTarget = (day, mealType) => {
  dragTarget.value = { day, mealType };
};

const clearDragTarget = () => {
  dragTarget.value = null;
};
</script>
```

---

## 🚀 WORKFLOW DE DÉPLOIEMENT DOCKER

### ❌ ANCIEN (Incomplet)
```bash
docker-compose down
git pull
docker-compose up -d --build
```

**Problèmes:**
- ❌ Pas de storage:link
- ❌ Pas de fix permissions
- ❌ Pas de cache clear
- ❌ Pas de seed unités
- ❌ Pas de npm build

---

### ✅ NOUVEAU (Complet)

**Utiliser le script automatique:**

```bash
./deploy-docker.sh
```

**OU manuellement:**

```bash
# 1. Pull code
git pull origin main

# 2. Rebuild containers
docker-compose down
docker-compose up -d --build

# 3. Attendre que les services démarrent
sleep 30

# 4. Setup Laravel
docker-compose exec laravel.test php artisan storage:link
docker-compose exec laravel.test php artisan migrate --force
docker-compose exec laravel.test php artisan db:seed --class=UnitSeeder

# 5. Caches
docker-compose exec laravel.test php artisan config:cache
docker-compose exec laravel.test php artisan route:cache
docker-compose exec laravel.test php artisan view:cache

# 6. Permissions
docker-compose exec laravel.test chmod -R 775 storage bootstrap/cache
docker-compose exec laravel.test chown -R www-data:www-data storage bootstrap/cache

# 7. Build assets
docker-compose exec laravel.test npm install
docker-compose exec laravel.test npm run build
```

---

## 📋 CHECKLIST APRÈS DÉPLOIEMENT

Tester manuellement :

- [ ] **Images:** Ouvrir une recette, les images s'affichent
- [ ] **Upload:** Créer/éditer recette, uploader image fonctionne
- [ ] **Unités:** Formulaire garde-manger affiche toutes les unités
- [ ] **Recettes:** Créer recette avec ingrédients + unités
- [ ] **Planning:** Drag & drop (même sans feedback visuel)
- [ ] **Mode cuisine:** Ouvrir mode pas-à-pas
- [ ] **Recherche:** Recherche de recettes fonctionne
- [ ] **Barcode:** Scanner code-barres (si API active)

---

## 🔍 COMMANDES DE DIAGNOSTIC

### Vérifier services Docker
```bash
docker-compose ps
docker-compose logs -f laravel.test
```

### Vérifier Laravel dans container
```bash
# Entrer dans container
docker-compose exec laravel.test bash

# Vérifier storage link
ls -la public/storage

# Vérifier permissions
ls -la storage/app/public

# Vérifier unités DB
php artisan tinker
>>> \App\Models\Unit::count();
>>> \App\Models\Unit::first();

# Vérifier config
php artisan config:show app.url
```

### Vérifier logs
```bash
# Logs Laravel
docker-compose exec laravel.test tail -f storage/logs/laravel.log

# Logs Nginx (si disponible)
docker-compose logs -f laravel.test | grep nginx
```

---

## 🛠️ COMMANDES UTILES

### Redémarrer un service
```bash
docker-compose restart laravel.test
docker-compose restart mysql
```

### Reconstruire un service spécifique
```bash
docker-compose up -d --build laravel.test
```

### Clear tout (attention, destructif)
```bash
docker-compose down -v  # ⚠️ Supprime volumes (DB)
docker system prune -a  # ⚠️ Nettoie tout Docker
```

### Accéder à MySQL
```bash
docker-compose exec mysql mysql -u sail -p recettes_zero_gaspi
# Password: password (défaut .env)
```

### Accéder à Redis
```bash
docker-compose exec redis redis-cli
> PING
> KEYS *
```

---

## ⚡ OPTIMISATIONS DOCKER

### Build plus rapide

Créer `.dockerignore` :
```
node_modules
vendor
storage/logs
storage/framework/cache
storage/framework/sessions
storage/framework/views
.git
.env
```

### Cache Composer
Ajouter dans `docker-compose.yml` :
```yaml
volumes:
  - '.:/var/www/html'
  - '~/.composer:/home/sail/.composer'  # Cache composer
```

---

## 📊 RÉSUMÉ PROBLÈMES CONNUS

| Problème | Cause | Solution | Status |
|----------|-------|----------|--------|
| 403 Forbidden storage | Pas de storage:link | `php artisan storage:link` | ✅ Documenté |
| Unités dropdown vide | unit.name inexistant | Accessor ajouté au model | ✅ Corrigé |
| Drag & drop "ne marche pas" | Manque feedback UX | Amélioration future | ⏳ Fonctionne |
| Après git pull + rebuild, bugs | Workflow incomplet | Utiliser deploy-docker.sh | ✅ Script créé |

---

**Prochain commit:** Fixes finaux + script déploiement
