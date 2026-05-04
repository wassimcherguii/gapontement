# Client translation system (GLanguages)

This document describes how **client-facing i18n** is modeled, stored, published, and consumed in the `Dashboardui_Backend_API` Laravel application. It complements (and does not duplicate) Laravel’s own `resources/lang/*` files used for **admin UI** strings.

---

## 1. Concepts

### 1.1 Translation domain

A **domain** is an isolated catalog of keys and per-locale values. Domains are intentionally separate so different surfaces (e.g. mobile app vs marketing web vs student portal) do not share one flat key namespace or one JSON bundle.

- **Canonical list:** Rows in the `translation_domains` table (`slug`, `name`, `sort_order`). The `slug` is what appears in URLs, API paths, and on-disk filenames (`public_languages_{slug}.json`, `i18n/{slug}/`).
- **Runtime registry:** `App\Services\TranslationDomainRegistry` exposes `allowedSlugs()` and `orderedDomains()` backed by `Cache::rememberForever` (invalidate after creating a domain via `TranslationDomainProvisioner`).
- **Bootstrap list:** [`config/translation_domains.php`](config/translation_domains.php) `domains` is **not** used as a runtime allowlist; it seeds initial rows in the migration [`2026_04_26_120000_create_translation_domains_table_and_migrate_keys.php`](database/migrations/2026_04_26_120000_create_translation_domains_table_and_migrate_keys.php) and [`TranslationDomainSeeder`](database/seeders/TranslationDomainSeeder.php).
- **Admin provisioning:** `GET/POST .../admin/assets/translation-domains` — creates a row, default `public_languages_{slug}.json`, and `jsonassets/i18n/{slug}/`.

Every domain has:

- One **locale catalog** JSON file on disk.
- One directory of **published locale bundles** on disk.
- `translation_keys` rows linked by **`translation_domain_id`** (FK to `translation_domains`).

### 1.2 Two storage layers

| Layer | Role |
|--------|------|
| **Database** | Authoring: domains, keys, descriptions, draft/published status, per-locale values. Supports admin forms, pagination, and coverage queries. |
| **JSON on disk** | Distribution: published nested JSON per `{slug, locale}`, plus **public languages** JSON per slug for “which locales exist” and metadata (name, native, direction, flag, optional `active`). |

Flow is **DB → JSON** via “Publish” and **JSON → DB** via “Import” (see §5).

### 1.3 Where published JSON is consumed

Three supported surfaces share the same on-disk bundles (`i18n/{slug}/{locale}.json`) but use different entrypoints:

| Surface | Mechanism | Typical domain |
|--------|-----------|----------------|
| **Mobile / SPA / external clients** | `GET /api/v1/i18n/{domain}/{locale}` | `mobile`, `web`, etc. |
| **Blade portals** (student, teacher, …) | `portal_t('dot.key')` after `SetTranslationPortal` sets `translation_portal` to a slug | Same slug as middleware |
| **Blade marketing / site shell** (e.g. welcome) | `web_client_t('dot.key')` — always reads the **`web`** slug | `web` only |

Laravel **`resources/lang/*` / `get_translation()`** remain for **admin UI** and framework strings; they are not replaced by client JSON.

---

## 2. On-disk layout (`jsonassets/`)

### 2.1 Public language catalog

**Path pattern:** `jsonassets/public_languages_{slug}.json`

Same JSON shape as before (default + supported map). Implemented in `App\Services\TranslationPublishService`.

### 2.2 Published bundles

**Path pattern:** `jsonassets/i18n/{slug}/{locale}.json`

Nested structure from dot keys in DB; publish writes only **published** keys/values.

### 2.3 Publish metadata

**Path:** `jsonassets/i18n/{slug}/_sync_meta.json`

---

## 3. Database model

### 3.1 `translation_domains`

| Column | Role |
|--------|------|
| `id` | PK |
| `slug` | Unique filesystem/URL token (`^[a-z0-9_-]+$`) |
| `name` | Admin display label |
| `sort_order` | Tab ordering |

Model: `App\Models\TranslationDomain` (`hasMany` `TranslationKey`).

### 3.2 `translation_keys`

- **`translation_domain_id`:** FK → `translation_domains.id` (`restrictOnDelete()` — do not delete a domain row while keys reference it).
- **`key`:** Dot-notation logical key; **unique per domain**: `unique(['translation_domain_id', 'key'])`.
- **`description`**, **`status`** (`draft` | `published`), **`version`**.

Eloquent: `App\Models\TranslationKey` with `translationDomain()` and `scopeDomain($query, string $slug)` (filters via related `translation_domains.slug`).

### 3.3 `translation_values`

Unchanged: FK to `translation_keys`, `locale`, `value`, `status`.

---

## 4. Services

### 4.1 `TranslationPublishService`

Still accepts **`$domain` as the slug string** for all path operations (`readLanguages`, `readBundle`, `exportDomain`, etc.).

### 4.2 `TranslationSyncService`

**`importDomainFromJson($slug, ...)`** resolves `TranslationDomain` by slug, then `TranslationKey::firstOrCreate(['translation_domain_id' => $id, 'key' => ...])`.

### 4.3 `TranslationSyncDiffService`

**`summarize(string $slug)`** returns JSON used by the admin “compare before sync” flow:

- **`publish`:** row-level differences between **what a full publish would write** (published keys/values in DB, active locales only) and **current nested JSON** on disk (flattened per locale). Each mismatch is `locale`, `key`, `database`, `json` (short labels, capped samples).
- **`import`:** for each scalar entry in flattened on-disk JSON, compares to the **published** value in DB for that key+locale; counts rows `importDomainFromJson` would change.
- **`meta_checksum_match` / `has_meta_checksum`:** compares `_sync_meta.json` checksum to `TranslationPublishService::computePublishedBundleChecksum()`.

Used by `GET .../admin/assets/client-translations/sync-diff?domain={slug}` (admin auth). The Client translations UI keeps **Publish** and **Import** disabled until the admin clicks **Compare database vs JSON**; the compare button remains enabled so the recap can be refreshed anytime. If both diff counts are zero, sync buttons stay off until the data changes and Compare is run again (or **Clear report** resets the panel).

### 4.4 `TranslationCoverageService`

**`localeCoverage($slug, $supported)`** — denominator/numerator unchanged logically; SQL joins `translation_domains` on `translation_keys.translation_domain_id` and filters `td.slug = $slug`.

### 4.5 `TranslationDomainRegistry` / `TranslationDomainProvisioner`

- **Registry:** Cached slug list + ordered models; `invalidate()` after provisioning.
- **Provisioner:** Inserts `translation_domains` row, writes default public languages JSON, creates `i18n/{slug}/`, then invalidates cache.

---

## 5. Admin HTTP routes

| Concern | Controller | Notes |
|--------|------------|--------|
| Domains list + create | `Admin\TranslationDomainController` | `translation-domains` routes |
| Key list + forms | `Admin\ClientTranslationController@index` | `?domain={slug}`; **Database** vs **Published JSON** tabs; sync banner compares `_sync_meta.json` checksum to `computePublishedBundleChecksum()` |
| Compare DB vs JSON (recap) | `ClientTranslationController@syncDiff` | `GET .../client-translations/sync-diff?domain={slug}` → JSON from `TranslationSyncDiffService::summarize()` |
| Full publish / import | `ClientTranslationController@syncToJson` / `syncFromJson` | `POST` with `domain`; **Publish** / **Import** start **disabled** until the admin runs **Compare database vs JSON**; each button enables only if the recap shows `publish.count > 0` or `import.count > 0`. **Compare** stays available for repeat checks. **Clear report** hides the recap and disables both sync buttons again. |
| Per-key DB edit + auto-publish | `ClientTranslationController@updateKey` | `PUT .../client-translations/keys/{translation_key}`; saves DB then `exportDomain` |
| Per-key JSON edit + auto-import | `ClientTranslationController@updateJsonKey` | `PUT .../client-translations/json-key`; merges flat key into locale files then `importDomainFromJson` |
| Keys CRUD / full sync / catalog | `ClientTranslationController` | Validates `domain` with `exists:translation_domains,slug`; **Add key** (`store`) also runs `exportDomain` after save |

Checksum logic: **published** keys/values only (same as a full publish), compared to the checksum stored in `i18n/{slug}/_sync_meta.json`. Draft keys are called out separately in the UI because they are excluded from JSON until published.

**UI:** `resources/views/admin/languages/client-translations.blade.php` — tab switcher for DB vs JSON matrices, modals for per-key edits, sync card with compare + recap tables, language catalog link.

---

## 6. Public REST API

| Endpoint | Notes |
|----------|--------|
| `GET /api/v1/i18n/{domain}/languages` | `{domain}` must match a DB slug; unknown slug → **404** via `Route::bind('domain', ...)` in [`AppServiceProvider`](app/Providers/AppServiceProvider.php). |
| `GET /api/v1/i18n/{domain}/{locale}` | Regex `where('domain', '[a-z0-9_-]{1,64}')` on route + bind validation. |

---

## 7. Server-rendered portals (student / teacher)

- **Middleware:** `SetTranslationPortal` — allows `$portal` only if `in_array($portal, TranslationDomainRegistry::allowedSlugs(), true)`.
- **`portal_t()`** — reads `i18n/{slug}/` for whatever slug the middleware bound; dot keys resolved with `portal_resolve_path()` (see `app/helpers/portal_translation_helpers.php`).

---

## 8. Server-rendered marketing site (`web` domain + `web_client_t`)

The language-prefixed **welcome** route (`GET /{lang}/`, name `welcome`) renders `resources/views/welcome.blade.php`: a small landing (navbar, hero, about, goal) whose copy comes **only** from the **`web`** domain bundles, not from `resources/lang`.

- **Helper:** `web_client_t(string $dotKey, ?string $default = null, ?string $locale = null)` in [`app/helpers/language_helpers.php`](app/helpers/language_helpers.php).
- **Source:** `TranslationPublishService::readBundle('web', $locale)` → nested JSON; lookup by dot path (`web_client_resolve_path`).
- **Caching:** per-request attributes (`_web_client_bundle_{locale}`) to avoid re-reading files on every string.
- **Fallback:** if the key is missing in the active locale, the helper tries **`en`**, then `$default`, then the literal `$dotKey`.
- **Locales / RTL:** Navbar language links use `readLanguages('web', false)` (same catalog as `public_languages_web.json`). Page `dir` uses `is_rtl_language()` from the global `languages.json` catalog.

**Suggested JSON layout** under `jsonassets/i18n/web/{locale}.json` (editable in admin or on disk, then publish/import as usual):

- `landing.meta.title` — `<title>`
- `landing.nav.*` — brand, section anchors, login label
- `landing.hero.*` — headline, subhead, CTAs
- `landing.about.*` / `landing.goal.*` — section titles and body
- `app.hint` — optional footer hint for editors

To change landing copy in production: edit keys in **Client translations → domain WEB** (or JSON files), then **Publish** so API, `web_client_t`, and any clients stay aligned.

---

## 9. Adding a new domain (checklist)

1. **Preferred:** Admin → **Translation domains** → create slug + name (provisions JSON + folder).
2. Add keys in **Client translations** for that slug; **Publish** when ready.
3. For Blade surfaces: `Route::middleware('portal:your_slug')` if the slug should drive `portal_t()`.
4. For the **public Laravel welcome / marketing** page: use slug **`web`** and keys under `landing.*` (consumed via `web_client_t()`); other domains are not read by that helper.

---

## 10. Operational notes

- **Cache:** After manual DB edits to `translation_domains`, run `php artisan cache:clear` or call registry invalidate (provisioning does this automatically).
- **FK restrict:** Deleting a domain row with existing keys will fail at the database until keys are removed or reassigned.
- **Composer autoload:** `portal_translation_helpers.php` and `language_helpers.php` (includes `web_client_t`) — see [`composer.json`](composer.json) `autoload.files`.

---

## 11. Quick reference — file map

| Artifact | Location |
|----------|-----------|
| Bootstrap slug list (seed only) | `config/translation_domains.php` |
| Domain rows | `translation_domains` table / `App\Models\TranslationDomain` |
| Slug registry + cache | `app/Services/TranslationDomainRegistry.php` |
| Provision new domain | `app/Services/TranslationDomainProvisioner.php` |
| Admin domains UI | `app/Http/Controllers/Admin/TranslationDomainController.php`, `resources/views/admin/languages/translation-domains.blade.php` |
| Locale catalog per slug | `jsonassets/public_languages_{slug}.json` |
| Published strings | `jsonassets/i18n/{slug}/{locale}.json` |
| Publish / read / write JSON | `app/Services/TranslationPublishService.php` |
| Import JSON → DB | `app/Services/TranslationSyncService.php` |
| DB vs JSON diff (admin recap) | `app/Services/TranslationSyncDiffService.php` |
| Coverage metrics | `app/Services/TranslationCoverageService.php` |
| Admin keys + sync + compare | `app/Http/Controllers/Admin/ClientTranslationController.php` |
| Admin client-translations Blade | `resources/views/admin/languages/client-translations.blade.php` |
| Public i18n API | `app/Http/Controllers/Api/ClientTranslationController.php` |
| Route model binding for `{domain}` | `AppServiceProvider::boot` |
| Portal middleware | `app/Http/Middleware/SetTranslationPortal.php` |
| Portal Blade helper (`portal_t`) | `app/helpers/portal_translation_helpers.php` |
| Web landing + `web_client_t` | `app/helpers/language_helpers.php`, `resources/views/welcome.blade.php` |
| Welcome feature test | `tests/Feature/WelcomeLandingTest.php` |
| Sync-diff feature test | `tests/Feature/ClientTranslationSyncDiffTest.php` |

This should be enough for engineers to trace behavior from HTTP entrypoints through services to disk and back.
