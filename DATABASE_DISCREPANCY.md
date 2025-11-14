# Database Discrepancy: PostgreSQL vs MySQL

## Issue

STATUS.md indicates the project uses **PostgreSQL** (lines 14, 122), but the actual implementation uses **MySQL**:

- `docker-compose.yml`: mysql service (line 30)
- `.env.example`: `DB_CONNECTION=mysql`
- Code contains MySQL-specific queries

## MySQL-Specific Code Found

### IngredientService.php (line 159)
```php
$query->whereRaw('MATCH(name) AGAINST(? IN BOOLEAN MODE)', [$searchTerm . '*'])
```

This uses MySQL FULLTEXT search, which is not compatible with PostgreSQL.

## Recommendations

### Option 1: Update Documentation (RECOMMENDED)
- Update STATUS.md to indicate MySQL instead of PostgreSQL
- Keep current MySQL implementation
- Pros: No code changes needed, system works as-is
- Cons: Documentation was incorrect

### Option 2: Migrate to PostgreSQL
- Update docker-compose.yml to use PostgreSQL
- Replace MySQL FULLTEXT with PostgreSQL Full-Text Search:
  ```php
  // PostgreSQL equivalent
  $query->whereRaw("to_tsvector('french', name) @@ plainto_tsquery('french', ?)", [$searchTerm]);
  ```
- Update .env.example
- Test all database interactions
- Pros: Matches original specification
- Cons: Requires testing and potential migration of existing data

## Decision

**Current status**: Using MySQL with no issues reported.

**Recommended action**: Update STATUS.md to reflect actual MySQL usage, as the application is working correctly with MySQL.
