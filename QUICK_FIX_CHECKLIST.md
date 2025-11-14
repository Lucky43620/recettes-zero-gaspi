# Vue Component Refactoring - Quick Fix Checklist

## All Issues Found

### 1. DUPLICATE COMPONENTS (Delete these)
- [ ] `/resources/js/Components/PrimaryButton.vue` - DELETE (use Common/PrimaryButton.vue)
- [ ] `/resources/js/Components/SecondaryButton.vue` - DELETE (use Common/SecondaryButton.vue)  
- [ ] `/resources/js/Components/TextInput.vue` - DELETE (use Common/FormInput.vue)
- [ ] `/resources/js/Components/Checkbox.vue` - DELETE (use Common/FormCheckbox.vue)
- [ ] `/resources/js/Components/DangerButton.vue` - MERGE (consolidate with button variants)

### 2. CONSOLIDATE THESE PAIRS
- [ ] `/resources/js/Components/Admin/StatCard.vue` + `/resources/js/Components/Dashboard/StatsCard.vue`
  - Keep StatCard.vue, delete StatsCard.vue
- [ ] `/resources/js/Components/Common/ActionButton.vue` + `/resources/js/Components/Dashboard/ActionButton.vue`
  - Keep Common version, delete Dashboard version

### 3. SPLIT THESE LARGE FILES

#### `/resources/js/Components/Recipe/CookingMode.vue` (215 lines)
Split into:
- [ ] CookingIngredientsPanel.vue
- [ ] CookingStepsPanel.vue  
- [ ] CookingStepTimer.vue
- [ ] CookingProgressBar.vue

#### `/resources/js/Components/Social/RatingStars.vue` (224 lines)
Split into:
- [ ] RatingDisplay.vue (readonly view)
- [ ] RatingEditor.vue (edit + review form)
- [ ] Keep ConfirmationModal for delete

#### `/resources/js/Components/Social/CommentSection.vue` (267 lines)
Split into:
- [ ] CommentForm.vue
- [ ] CommentList.vue
- [ ] CommentItem.vue (with voting)
- [ ] CommentReplies.vue (nested replies)

#### `/resources/js/Components/Social/CooksnapSection.vue` (223 lines)
Split into:
- [ ] PhotoUploadForm.vue
- [ ] CooksnapGallery.vue
- [ ] CooksnapCard.vue (individual photo)

#### `/resources/js/Components/Notifications/NotificationBell.vue` (138 lines)
Split into:
- [ ] NotificationItem.vue
- [ ] NotificationIcon.vue (or use @heroicons)

### 4. CREATE THESE REUSABLE COMPONENTS
- [ ] `Common/ImagePlaceholder.vue` - Used in 4+ components
- [ ] `Common/LoadingSpinner.vue` - Used in 3+ components
- [ ] `Common/ModalOverlay.vue` - Modal backdrop pattern
- [ ] `Common/Modals/FormModal.vue` - Generic add/edit modal wrapper

### 5. FIX HARDCODED VALUES

#### Hardcoded French text (needs i18n)
- [ ] `/resources/js/Components/Notifications/NotificationBell.vue`
  - Line 21: "Notifications"
  - Line 29: "Tout marquer comme lu"
  - Line 34: "Aucune notification"
  - Line 73: "Voir toutes les notifications"

#### Hardcoded language data
- [ ] `/resources/js/Components/Common/LanguageSwitcher.vue` (Lines 8-14)
  - Move language list to i18n config

#### Hardcoded icon paths
- [ ] `/resources/js/Components/Common/EmptyState.vue` (Lines 27-31)
  - Replace with @heroicons/vue imports

#### Hardcoded colors
- [ ] `/resources/js/Components/Recipe/CookingMode.vue`
  - Lines 93, 117, 158 - Use design system variables
- [ ] `/resources/js/Components/Recipe/RecipeTimer.vue` (Lines 110-129)
  - Replace hardcoded colors with CSS variables

### 6. FIX NAMING INCONSISTENCIES
- [ ] Rename `/resources/js/Components/Pantry/BarcodeScanner.vue` → `BarcodeModal.vue`
- [ ] Organize all modal components under `Common/Modals/`
- [ ] Move all button components into variant-based `Common/Button.vue`

### 7. REORGANIZE DIRECTORY STRUCTURE

Current structure (messy):
```
Components/
├── PrimaryButton.vue (root level - should be deleted)
├── SecondaryButton.vue (root level - should be deleted)
├── Modal.vue (root level)
├── ... 19 more root components ...
├── Common/ (inconsistent)
├── Pantry/
├── Recipe/
├── Social/
└── ...
```

Target structure:
```
Components/
├── Common/
│   ├── Buttons/
│   │   └── Button.vue (variant="primary|secondary|danger")
│   ├── Forms/
│   │   ├── FormInput.vue
│   │   ├── FormCheckbox.vue
│   │   ├── FormRadio.vue
│   │   ├── FormSelect.vue
│   │   └── FormTextarea.vue
│   ├── Modals/
│   │   ├── Modal.vue
│   │   ├── DialogModal.vue
│   │   ├── ConfirmationModal.vue
│   │   └── FormModal.vue
│   ├── Feedback/
│   ├── Navigation/
│   ├── Layout/
│   └── Utilities/
├── Pantry/
├── Recipe/
├── Social/
├── Ingredients/
├── Admin/
├── Dashboard/
└── Notifications/
```

---

## Implementation Order

### Week 1 - CRITICAL
- [ ] Day 1: Remove duplicate PrimaryButton (root), SecondaryButton (root)
- [ ] Day 2: Remove TextInput, Checkbox (root versions)  
- [ ] Day 3: Consolidate StatCard components
- [ ] Day 4: Merge ActionButton versions
- [ ] Day 5: Update all imports after deletions

### Week 2 - HIGH PRIORITY
- [ ] Day 1: Split CookingMode.vue (4 files)
- [ ] Day 2: Split RatingStars.vue (3 files)
- [ ] Day 3: Split CommentSection.vue (4 files)
- [ ] Day 4: Split CooksnapSection.vue (3 files)
- [ ] Day 5: Merge Add/Edit PantryItemModal

### Week 3 - MEDIUM PRIORITY
- [ ] Create ImagePlaceholder.vue
- [ ] Create LoadingSpinner.vue
- [ ] Fix NotificationBell i18n strings
- [ ] Fix CookingMode hardcoded colors
- [ ] Create FormModal wrapper

### Week 4+ - CLEANUP
- [ ] Consolidate icon usage (@heroicons/vue)
- [ ] Move language data to config
- [ ] Extract reusable patterns
- [ ] Rename BarcodeScanner → BarcodeModal
- [ ] Full directory reorganization

---

## Testing Checklist

After each change:
- [ ] Run `npm run dev` (no build errors)
- [ ] Check components still render correctly
- [ ] Verify no import errors in console
- [ ] Test functionality still works
- [ ] Commit with clear message

---

## Files Generated By This Analysis

1. **COMPONENT_ANALYSIS.md** - Detailed report (7 sections, 200+ lines)
2. **COMPONENT_ISSUES_SUMMARY.txt** - Quick reference with line numbers
3. **QUICK_FIX_CHECKLIST.md** - This file (implementation roadmap)

---

## Summary Statistics

- **Total Components Analyzed**: 65
- **Duplicate Components**: 4 conflicts
- **Components to Delete**: 5
- **Components to Split**: 5 (total 19 new components)
- **Large Files**: 5 files > 200 lines
- **Hardcoded Values Found**: 20+ instances
- **Inconsistent Names**: 12+ components
- **Root-level components to move**: 19

---

**Estimated Effort**: 4-6 weeks (with testing)
**Priority**: CRITICAL (refactoring improves maintainability significantly)

