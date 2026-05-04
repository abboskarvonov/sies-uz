# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack

- **Laravel 12**, PHP 8.2+, MySQL
- **Filament v4** (admin panel) — `bezhansalleh/filament-shield` for permissions
- **Livewire 3** + **Alpine.js** (bundled via Livewire, not npm)
- **Jetstream** (profile/auth UI), **Laravel Socialite** (HEMIS OAuth)
- **mcamara/laravel-localization** — locales: `uz`, `ru`, `en`
- **Intervention Image v3** (GD) for WebP, **spatie/eloquent-sortable**, **spatie/laravel-permission**
- **Vite + TailwindCSS v4**

## Commands

```bash
# Development (runs server + queue + pail + vite concurrently)
composer run dev

# Build assets for production
npm run build

# Run all tests (clears config first)
composer test

# Run a single test file or filter
php artisan test --filter=ExampleTest
php artisan test tests/Feature/ExampleTest.php

# Lint/format PHP
vendor/bin/pint

# HEMIS sync
php artisan hemis:sync-departments [--dry-run]
php artisan hemis:sync-employees [--skip-photos] [--dry-run]

# Filament Shield (permissions)
php artisan shield:generate --all --panel=admin

# Deploy to production
bash deploy.sh
```

## Architecture

### URL & Navigation Structure

```
/{locale}/{menu}/{submenu}/{multimenu}/{page}/staff/{staff}
```

Models: `Menu → Submenu → Multimenu → Page`. All four are in the `pages` table (`Page` model) with `page_type` discriminating: `default`, `blog`, `faculty`, `department`, `center`, `section`, `boshqarma`.

### Filament Admin

All content types (Faculty, Department, Center, Section, Boshqarma, Page, Blog) share one `Page` model and one `BasePageResource`. Each resource child class sets `$pageTypes = ['faculty']` etc., which filters Eloquent queries and hides the type selector. See `app/Filament/Resources/BasePageResource.php`.

Authorization uses Spatie permissions with prefix `{permissionPrefix}.viewAny/create/update/delete`. Users with `manage_own_assigned_pages` can only see pages assigned to them via `user_page_positions` or `assigned_pages`.

### Localization

- All title/content/slug fields are triplicated: `title_uz`, `title_ru`, `title_en`
- Route prefix set by `LaravelLocalization::setLocale()` — all public routes are inside this group
- Helper functions in `app/Helpers/LocalizationHelper.php`: `lc_title($model)`, `localized_url()`, `localized_page_route()`
- Use ASCII apostrophes only — Unicode curly quotes break the helpers

### Alpine + Livewire (CRITICAL)

**Never** `import alpinejs` from npm or call `Alpine.start()` in app.js. Livewire 3 bundles its own Alpine. Import both from Livewire's ESM bundle:

```js
import { Livewire, Alpine } from "../../vendor/livewire/livewire/dist/livewire.esm";
Alpine.plugin(Intersect);
Livewire.start();
```

`@livewireScripts` is removed from the layout — `Livewire.start()` in app.js handles it.

### View Composers (AppServiceProvider)

Registered in `boot()`:
- `components.main.navbar` → `$menus` (header menus)
- `components.main.header/footer/nav-logo` → `$siteSettings` (cached with `static $settings`)
- `components.main.quick-links` → `$quickLinks`
- `*` → `$symbolSlugs` (flag/emblem/anthem slugs — runs on every view)

### HEMIS Integration

- **HemisApiService** — paginated REST API, Bearer token from `HEMIS_API_TOKEN`
- `HemisSyncDepartments` maps HEMIS department types to `page_type` via `TYPE_MAP` (code 11→faculty, 12→department, 13→section; name contains 'markaz'→center)
- `HemisSyncEmployees` groups by UUID → one User per employee, all positions in `user_page_positions`
- **OAuth**: `hemis-employee` driver → `hemis.sies.uz`, `hemis-student` driver → `student.sies.uz`
- Employee `hasVerifiedEmail()` always returns `true` (override on User model)

### User & Positions

- `user_page_positions` table: `user_id + page_id UNIQUE`, `is_primary` flag
- `User::pagePositions()` HasMany, `User::primaryPosition()` HasOne
- `User::hasAccessToPage($record)` — used in all BasePageResource permission checks

### Images & Storage

- Laravel 12 local disk root: `storage/app/private` (not `storage/app`)
- Livewire temp uploads: `storage/app/private/livewire-tmp/`
- Profile photos: `storage/app/public/profile-photos/` (public disk)
- `APP_URL` must use `https://` — HTTP causes Livewire signed URL mixed-content failures
- WebP conversion and srcset helpers in `app/Helpers/ImageHelper.php`

### Security

- `TrustHosts` and `SecurityHeaders` middleware appended globally in `bootstrap/app.php`
- CSP is report-only — check browser console before switching to enforcing
- Email addresses obfuscated via base64 in `data-e` attributes, decoded by JS
- Admin login protected by `RateLimiter` (`api-login`: 5/min per email)

### Slug Generation

`app/Helpers/SlugHelper.php` — single LIKE query + `pluck()->flip()` for O(1) uniqueness check. Each localized slug field (`slug_uz`, `slug_ru`, `slug_en`) is generated independently on save.

### SiteSettings Singleton

`App\Models\SiteSettings::instance()` → `firstOrCreate([])`. Fields: `site_name_uz/ru/en`, `address_uz/ru/en`, `phone_primary/secondary`, `email_primary/secondary`, social URLs, `logo`, `map_embed_url`. Managed via `SiteSettingsResource` (canCreate returns false when record exists).
