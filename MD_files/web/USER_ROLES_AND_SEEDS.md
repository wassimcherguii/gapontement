# User roles & seeds (appointment platform)

**Last updated:** 2026-05-03  

This document tracks how application user roles are stored, migrated from the legacy teaching stack, and seeded for local/staging work.

---

## Current roles

| Role slug   | Typical use |
|------------|-------------|
| `superadmin` | Platform-level access |
| `admin`      | Tenant / clinic administrator |
| `doctor`     | Provider |
| `secretary`  | Front desk / staff |
| `patient`    | Person booking or receiving care |
| `companion`  | Family / escort (optional product role) |

Canonical list in code: `App\Models\User::getAvailableRoles()`.

---

## Database column

- **Column:** `users.role`
- **Type:** `VARCHAR(32)` (not an enum)
- **Default:** `patient`

### Migration from legacy values

Migration file: `database/migrations/2026_05_03_120000_convert_users_role_to_string_appointment_roles.php`

If upgrading from the old enum (`visitor`, `manager`, `admin`, `superadmin`):

| Old value   | New value   |
|------------|-------------|
| `visitor`  | `patient`   |
| `manager`  | `secretary` |
| `admin`    | `admin`     |
| `superadmin` | `superadmin` |

**Rollback:** `down()` is intentionally not reversible automatically (throws). Do not rely on `migrate:rollback` for this migration once new roles exist in data.

---

## Access helpers (`App\Models\User`)

- **`hasAdminPrivileges()`** — `admin` or `superadmin` only. Used for the existing Laravel admin dashboard (`EnsureUserIsAdmin` middleware).
- **`hasStaffPrivileges()`** — `doctor`, `secretary`, `admin`, `superadmin`. Reserved for future staff-only routes/policies (not wired to admin UI yet).
- **`isSuperAdmin()`**, **`isAdmin()`**, **`isDoctor()`**, **`isSecretary()`**, **`isPatient()`**, **`isCompanion()`** — role checks.

---

## Seeded users (`UserRoleSeeder`)

**Password for every seeded account below:** `password`

| Role        | Email                    |
|------------|--------------------------|
| superadmin | `superadmin@example.com` |
| admin      | `admin@example.com`      |
| doctor     | `doctor@example.com`     |
| secretary  | `secretary@example.com`  |
| patient    | `patient@example.com`    |
| companion  | `companion@example.com`  |

`DatabaseSeeder` also ensures **`test@example.com`** exists as **`patient`** with password **`password`**.

### Optional bulk patients

`VisitorUsersSeeder` (name kept for history) creates **50 random `patient` users** with password `password`. Enable by uncommenting it in `Database\Seeders\DatabaseSeeder`.

---

## API defaults

- **Registration** (`Api\AuthController@register`): new users get role **`patient`**.
- **User update** (`Api\UserController@update`): `role` must be one of `User::getAvailableRoles()`; only **`superadmin`** may change `role` on updates.

---

## UI demo block

`resources/views/components/login-form.blade.php` lists the six demo accounts for admin login screens.

---

## Commands

**Existing database (apply new migration + refresh role seeds):**

```bash
cd Dashboardui_Backend_API
php artisan migrate
php artisan db:seed --class=UserRoleSeeder
```

**Full reset (destroys all data):**

```bash
php artisan migrate:fresh --seed
```

---

## Related files

| Area | Path |
|------|------|
| User model | `app/Models/User.php` |
| Role migration | `database/migrations/2026_05_03_120000_convert_users_role_to_string_appointment_roles.php` |
| Original enum migration (historical) | `database/migrations/2025_10_21_222638_add_role_to_users_table.php` |
| Seeds | `database/seeders/UserRoleSeeder.php`, `database/seeders/DatabaseSeeder.php` |
| Factory default | `database/factories/UserFactory.php` |
| Bulk patients | `database/seeders/VisitorUsersSeeder.php` |

---

## Notes for future work

- **Doctor / secretary** do not automatically receive the Laravel **admin** UI; only `admin` and `superadmin` do today. Add dedicated routes/middleware when staff portals exist.
- Consider **translation domain** slugs (`student` / `teacher` vs `patient` / `provider`) separately from **user roles** — they are different concepts (i18n bundles vs `users.role`).
