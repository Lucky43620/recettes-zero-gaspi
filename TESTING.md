# Guide de Tests - Recettes Zéro Gaspi

## 📋 Configuration des Tests

Les tests utilisent une base de données séparée pour éviter d'altérer les données de développement.

### Prérequis

- PHP 8.4+ avec extensions:
  - `pdo_sqlite` (recommandé) OU `pdo_pgsql` / `pdo_mysql`
  - `bcmath` (pour Laravel Cashier)

### Installation Extension SQLite (Recommandé)

**Ubuntu/Debian:**
```bash
sudo apt-get install php8.4-sqlite3
```

**macOS (Homebrew):**
```bash
brew install php@8.4
```

**Windows:**
- Décommenter `;extension=pdo_sqlite` dans `php.ini`
- Décommenter `;extension=sqlite3` dans `php.ini`

## 🚀 Lancer les Tests

### Option 1: SQLite (Plus Rapide - Recommandé)

La configuration actuelle dans `phpunit.xml` utilise SQLite en mémoire :

```bash
php artisan test
```

**Avec parallélisation (plus rapide):**
```bash
php artisan test --parallel
```

### Option 2: PostgreSQL

Si vous préférez tester avec PostgreSQL (comme en production):

1. **Démarrer PostgreSQL:**
```bash
# Ubuntu/Debian
sudo service postgresql start

# macOS
brew services start postgresql

# Docker
docker run --name recettes-test-db -e POSTGRES_PASSWORD=secret -p 5432:5432 -d postgres:16
```

2. **Créer la base de test:**
```bash
createdb recettes_test
```

3. **Modifier phpunit.xml:**
```xml
<env name="DB_CONNECTION" value="pgsql"/>
<env name="DB_DATABASE" value="recettes_test"/>
<env name="DB_USERNAME" value="postgres"/>
<env name="DB_PASSWORD" value="secret"/>
```

4. **Lancer les tests:**
```bash
php artisan test
```

## 📊 Suites de Tests

### Tests Unitaires (Rapides)
```bash
php artisan test --testsuite=Unit
```

### Tests de Fonctionnalités (Complets)
```bash
php artisan test --testsuite=Feature
```

### Tests Spécifiques

**Par fichier:**
```bash
php artisan test tests/Feature/Recipe/RecipeTest.php
```

**Par méthode:**
```bash
php artisan test --filter test_users_can_create_recipes
```

## 🔍 Couverture de Tests

### Statistiques Actuelles

| Suite | Nombre | Description |
|-------|--------|-------------|
| **Feature** | 175+ | Tests d'intégration complets |
| **Unit** | 1+ | Tests unitaires isolés |

### Domaines Couverts

✅ **Authentification & Sécurité**
- Login, Register, 2FA
- Reset password, Email verification
- API tokens, Permissions

✅ **Recettes (CRUD)**
- Création, édition, suppression
- Validation des données
- Upload médias
- Visibilité publique/privée

✅ **Planning & Listes**
- Meal plans hebdomadaires
- Shopping lists
- Génération automatique

✅ **Garde-Manger**
- Ajout/édition items
- Alertes expiration
- Suggestions anti-gaspi

✅ **Communauté**
- Followers/Following
- Comments & Ratings
- Cooksnaps
- Collections & Favoris

✅ **Modération**
- Système de signalement
- Admin panel
- Gestion badges

✅ **Abonnements (Jalon 8)**
- Stripe checkout
- Gestion abonnements
- Paywall Premium

## 🐛 Résolution de Problèmes

### Erreur: "could not find driver"

**Solution:** Installer l'extension PDO appropriée

```bash
# Pour SQLite
sudo apt-get install php8.4-sqlite3

# Pour PostgreSQL
sudo apt-get install php8.4-pgsql

# Vérifier les extensions chargées
php -m | grep -i pdo
```

### Erreur: "Connection refused"

**PostgreSQL non démarré:**
```bash
sudo service postgresql start
```

**Base de données manquante:**
```bash
createdb recettes_test
```

### Tests Lents

**Utiliser SQLite en mémoire** (10x plus rapide):
```xml
<!-- phpunit.xml -->
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

**Parallélisation:**
```bash
php artisan test --parallel
```

## 📝 Écrire de Nouveaux Tests

### Structure

```php
<?php

namespace Tests\Feature\Recipe;

use App\Models\User;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_create_recipes(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/recipes', [
            'title' => 'Test Recipe',
            'description' => 'Test description',
            'difficulty' => 'easy',
            'category' => 'dessert',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('recipes', [
            'title' => 'Test Recipe',
        ]);
    }
}
```

### Conventions

- **Nom du fichier:** `{Feature}Test.php`
- **Nom de la méthode:** `test_{action}_{expected_result}`
- **Utiliser factories:** Pour créer des données test
- **RefreshDatabase:** Réinitialise la DB entre chaque test
- **Assertions:** Vérifier comportements attendus

## 🔐 Tests de Sécurité

### Authentification Requise

```php
public function test_guests_cannot_create_recipes(): void
{
    $response = $this->post('/recipes', []);
    $response->assertRedirect('/login');
}
```

### Autorisations

```php
public function test_users_cannot_edit_others_recipes(): void
{
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $recipe = Recipe::factory()->for($owner)->create();

    $response = $this->actingAs($other)->put("/recipes/{$recipe->slug}", []);
    $response->assertStatus(403);
}
```

### Validation

```php
public function test_recipe_title_is_required(): void
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/recipes', [
        'title' => '', // Invalid
    ]);

    $response->assertSessionHasErrors('title');
}
```

## 📈 CI/CD

### GitHub Actions

Les tests s'exécutent automatiquement sur chaque push:

```yaml
name: Tests

on: [push, pull_request]

jobs:
  tests:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v3
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          extensions: sqlite, pdo_sqlite

      - run: composer install
      - run: php artisan test
```

## 🎯 Objectif: 100% Coverage

Pour maintenir la qualité du code, visez:

- ✅ **100% des features critiques** testées
- ✅ **Toutes les routes** couvertes
- ✅ **Cas limites** vérifiés
- ✅ **Sécurité** validée

---

**Documentation complète:** https://laravel.com/docs/11.x/testing
