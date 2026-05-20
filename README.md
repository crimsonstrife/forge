![Forge Logo](public/logos/PNG/forge-03.png)

[![DeepSource](https://app.deepsource.com/gh/crimsonstrife/forge.svg/?label=code+coverage&show_trend=true&token=SVWr1G8gJyQiCJ9ATcG0qdbl)](https://app.deepsource.com/gh/crimsonstrife/forge/)
[![License: AGPL-3.0](https://img.shields.io/badge/license-AGPL--3.0-blue)](https://choosealicense.com/licenses/agpl-3.0/)
![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777bb3?logo=php)
![Laravel 12](https://img.shields.io/badge/Laravel-12-ff2d20?logo=laravel)
![Filament 4](https://img.shields.io/badge/Filament-4-0ea5e9)
[![Laravel](https://github.com/crimsonstrife/forge/actions/workflows/laravel.yml/badge.svg?branch=prod)](https://github.com/crimsonstrife/forge/actions/workflows/laravel.yml)

**Forge** is a Laravel 12 app for projects, issues, goals, support, and internal operations. It combines day-to-day execution, planning views, support intake, machine APIs, and a Filament admin panel in one app.

> **Note:** This project is **not** affiliated with Laravel Forge.

## What Forge Includes

- **Dashboard workspaces** with role-aware landing views, a personal "Today" surface, recent activity, and operational summaries.
- **Projects and planning** with board, backlog, scrum, calendar, timeline, roadmap, transitions, milestone, and analytics/reporting views.
- **Issue management** with Issue Explorer, saved views, comments, attachments, action items, issue links, time tracking, and collaboration panels.
- **Goals, notes, organizations, and search** for work above the single-project level.
- **Support desk workflows** including public ticket intake, staff triage, support access links, and ticket-to-issue conversion.
- **Feedback boards** for product ideas and bug reports with magic-link identities, voting, comments, moderation, and issue escalation.
- **Repository and docs integrations** including GitHub webhooks, Crucible repository/branch/PR linking, and Codex page linking.
- **Admin and operations** through Filament, health/status pages, reporting exports, onboarding tours, and API docs tooling.
- **APIs** for public project issue feeds, support ticket ingest, authenticated project/issue access, and system-to-system project reads.

## Tech Stack

- **Backend:** PHP 8.3, Laravel 12, Livewire 3, Volt, Jetstream 5, Folio, Filament 4
- **Frontend:** Bootstrap 5, Tailwind CSS 4, Web Awesome components, TinyMCE 8
- **Build tooling:** Vite 7, `laravel-vite-plugin`, `vite-plugin-static-copy`, esbuild
- **Default local setup:** SQLite, database-backed cache/session/queue, Reverb broadcasting, log mailer

> **Node requirement:** Vite 7 requires Node 20+. `npm ci` with the checked-in lockfile is the expected install path.

## Local Development

1. **Clone and install dependencies**

   ```bash
   git clone https://github.com/crimsonstrife/forge.git
   cd forge
   cp .env.example .env
   composer install
   npm ci
   ```

2. **Create the local database and app key**

   ```bash
   mkdir -p database
   touch database/database.sqlite
   php artisan key:generate
   php artisan storage:link
   ```

3. **Migrate and seed**

   ```bash
   php artisan migrate --seed
   ```

4. **Start the development stack**

   ```bash
   composer dev
   ```

   `composer dev` starts the Laravel server, queue listener, `pail`, and Vite together.

5. **Optional realtime server**

   ```bash
   php artisan reverb:start
   ```

## Frontend Build Notes

Use `npm run build` for production assets.

This project's build does more than a plain Vite compile:

- Builds the Laravel Vite entrypoints for app, editor init, and TinyMCE plugin assets.
- Copies Web Awesome and TinyMCE vendor assets into the public build output.
- Minifies copied TinyMCE vendor JS/CSS after the Vite build.
- Compiles stable TinyMCE plugin bundles into `public/plugins`.

If you are actively editing files in `resources/tiny-plugins`, run:

```bash
npm run dev:plugins
```

## Testing

```bash
composer test
```

The test suite uses in-memory SQLite via `phpunit.xml`.

## Configuration Notes

- `.env.example` is SQLite-first. Redis is optional for local development.
- Cache, session, and queue defaults use Laravel's database drivers locally.
- Reverb, social OAuth, Codex, and Crucible integrations only need configuration if you plan to use them.
- CI currently installs PHP and Node dependencies, runs `npm run build`, migrates against SQLite, and performs package discovery.

## DigitalOcean Valkey

When pointing Forge at DigitalOcean Managed Valkey, keep Laravel's cache, queue, and session drivers set to `redis` and configure the connection itself for `phpredis` over TLS:

```env
REDIS_CLIENT=phpredis
REDIS_SCHEME=tls
REDIS_HOST=your-cluster.db.ondigitalocean.com
REDIS_PORT=25061
REDIS_PASSWORD=your_password
REDIS_DB=0
REDIS_CACHE_DB=1
```

- Do not prefix `REDIS_HOST` with `tls://`; the scheme is configured separately through `REDIS_SCHEME`.
- Forge now requires the `phpredis` extension via Composer's `ext-redis` platform requirement. Install a recent build with TLS support on your target hosts.
- DigitalOcean uses Let's Encrypt certificates, so no custom CA bundle or disabled peer verification should be necessary.
- If you want a local non-TLS Redis instance instead, override `REDIS_SCHEME=tcp`, `REDIS_HOST=127.0.0.1`, and `REDIS_PORT=6379` in your local `.env`.

## License

GNU Affero General Public License v3.0
