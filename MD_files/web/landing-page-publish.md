# Landing homepage: CMS, publish, and JSON cache

## Overview

Homepage marketing content is stored in the database (`landing_*` tables). **Publishing** writes one JSON file per locale under `jsonassets/page-cache/`. The public route `GET /{lang}/` renders `welcome.blade.php`, which reads **`home.{locale}.json`** via `landing_page_payload('home')` so page views do not query the CMS tables.

## Commands

Publish (or re-publish) after editing in admin:

```bash
php artisan landing:publish home
```

## Files on disk

| Path | Purpose |
|------|---------|
| `jsonassets/page-cache/home.en.json` | Published English bundle |
| `jsonassets/page-cache/home.fr.json` | Published French bundle |
| `jsonassets/page-cache/home.ar.json` | Published Arabic bundle |
| `jsonassets/page-cache/home._meta.json` | `generated_at`, `checksum`, locale list |

## Admin

**Website → Home page** (`/{lang}/admin/website/home`): edit meta, sections, navigation, entities, then **Save**. Use **Publish homepage** to write JSON files.

## Deploy notes

- Include `jsonassets/page-cache/` in deploy artifacts **or** run `php artisan landing:publish home` on the server after migrations and seeding so files exist before traffic hits the site.
- If JSON files are missing, the welcome page falls back to **`web`** client translations (`web_client_t`) so the site still loads.

## Seeding

`Database\Seeders\LandingHomeSeeder` creates a minimal `home` page. Run `php artisan db:seed --class=LandingHomeSeeder` (or full `db:seed`), then publish.
