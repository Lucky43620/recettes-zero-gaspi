# Roadmap de développement — avec cases à cocher

> Généré le 2025-10-29 16:32 (Europe/Paris)

## Jalon 0 — Fondations (S1)
- [x] **Repo & CI/CD** (monorepo, Docker, lint/test, staging)
- [x] **Base Postgres & migrations** (appliquer `schema_recipes_app.sql`, seeds min)
- [x] **Auth & RGPD** (inscription/login, consentements, pages légales)

## Jalon 1 — Recettes cœur (S2–S3)
- [x] CRUD Recettes (brouillon/public, étapes, ingrédients)
- [x] Upload médias (S3-like), miniatures, purge orphelins (cron)
- [x] Recherche de base (titre, auteur, tags) + tris (durée/difficulté/popularité)

## Jalon 2 — Social essentiel (S4–S5)
- [x] Profils publics + suivi (follow/unfollow) + flux perso minimal
- [x] Notes (1–5) + avis, commentaires & réponses (modération soft-delete)
- [x] Favoris & Collections (publiques/privées, ordre manuel)

## Jalon 3 — Planning & liste de courses (S6–S8)
- [x] Planning hebdomadaire (drag&drop, types de repas)
- [x] Génération liste de courses (agrégation, fusion d'unités)
- [x] Historique/duplication de semaine

## Jalon 4 — Garde-manger & anti-gaspi (S9–S11)
- [ ] Inventaire (quantités, unité, DLC, lieu, code-barres)
- [ ] Alertes péremption (job quotidien + notifs)
- [ ] Recherche « avec mes ingrédients » (+ manquants)
- [ ] Intégration scan code-barres (OpenFoodFacts)

## Jalon 5 — UX cuisine & notifications (S12–S13)
- [ ] Mode pas-à-pas avec minuteurs
- [ ] Notifications (réponses, nouveaux suivis, rappel repas, push PWA)
- [ ] PWA (manifest, offline pour favoris/planning)

## Jalon 6 — Communauté & gamification (S14–S15)
- [ ] Cooksnaps (réalisations des utilisateurs)
- [ ] Événements/concours (CRUD, inscription, classement simple)
- [ ] Badges (règles & attribution visibles sur profil)

## Jalon 7 — Modération, sécurité & perf (S16)
- [ ] Signalements & actions admin (workflow open/closed)
- [ ] RGPD complet (export/suppression de compte/données, cookies)
- [ ] Perfs & recherche avancée (index GIN/TRGM, pré-calculs, option Elastic/Algolia)

## Jalon 8 — Monétisation & intégrations (S17–S18, option)
- [ ] Freemium/Premium (subscriptions, entitlements, paywall)
- [ ] Export liste vers partenaires (placeholder / future intégration)

---

### Transverses (en continu)
- [ ] Accessibilité (a11y) & i18n
- [ ] Tests unitaires / intégration / e2e
- [ ] Observabilité (logs, métriques, tracing)
- [ ] Sécurité (rate-limit, CSP, CSRF, antivirus upload)
- [ ] CDN images & politique cache
