# Contributing Guide

Conventions for the USM Pharmacy team — follow these to avoid conflicts across the three independent modules.

---

## Branch Naming

```
feature/<module>-<short-description>
fix/<module>-<short-description>
chore/<what>
```

Examples:
- `feature/prescription-inline-patient-reg`
- `feature/inventory-batch-receive-ui`
- `fix/pos-is-active-column`
- `chore/update-progress-docs`

Use your module name as the prefix so branches are easy to trace. Module prefixes: `auth`, `prescription`, `pos`, `inventory`, `risk-engine`, `patient-portal`, `chatbot`.

---

## Commit Messages

Follow the format:
```
<type>(<scope>): <short imperative summary>

Optional longer explanation.
```

Types: `feat`, `fix`, `refactor`, `test`, `docs`, `chore`, `style`

Examples:
```
feat(prescription): add inline patient registration on create form
fix(pos): add missing is_active column migration to medicines table
test(prescription): add test for routing a prescription with insufficient stock
docs(progress): update module status table after POS completion
```

---

## Pull Request Process

1. **Target branch:** all PRs merge into `main`.
2. **Reviewer:** at least one other team member must approve before merging.
3. **PR size:** keep PRs to one feature or one fix. Do not bundle unrelated changes.
4. **Before opening a PR:**
   - Run `php artisan test` — all tests must pass.
   - Run `vendor/bin/pint --dirty` — fix any formatting issues.
   - Update `docs/PROGRESS.md` with your module's new status.
5. **Draft PRs:** open a draft PR early if you need feedback on a direction before it's ready to merge.

---

## Avoiding Migration Conflicts

Migrations are time-stamped. If two teammates create a migration at roughly the same time, timestamps may collide or foreign-key order may break.

- **Announce** when you are about to create a migration in the team chat so others can wait.
- **Never** hand-edit another teammate's migration file. If their schema needs a change, add a new migration on top.
- If you need to build on a table owned by another member (e.g., adding a column to `medicines` for the risk engine), create your own migration file and add only your column — do not edit the original migration.
- When pulling fresh branches, always run `php artisan migrate` (not `migrate:fresh`) to layer your local DB forward rather than wiping it.

---

## Test File Conventions

- Feature tests live in `tests/Feature/`. Name them `<Module>ModuleTest.php` (e.g., `InventoryModuleTest.php`).
- Do not put tests inside another member's existing test file — create a new file for your module.
- Use factories and roles, never rely on the seeded demo accounts — seeders are for manual testing only.
- Run only your own test file during development: `vendor/bin/pest tests/Feature/YourModuleTest.php`.

---

## Before Merging a Completed Feature

- [ ] `php artisan test` passes (all tests green).
- [ ] `vendor/bin/pint --dirty` has been run.
- [ ] `docs/PROGRESS.md` module status row updated.
- [ ] Any new Open Decisions or conventions noted in `docs/PROGRESS.md`.
- [ ] `docs/ARCHITECTURE.md` updated if you added tables or service classes.
