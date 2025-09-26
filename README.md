![Forge Logo](/public/logos/PNG/forge-03.png)

[![DeepSource](https://app.deepsource.com/gh/crimsonstrife/forge.svg/?label=code+coverage&show_trend=true&token=SVWr1G8gJyQiCJ9ATcG0qdbl)](https://app.deepsource.com/gh/crimsonstrife/forge/)
[![License: AGPL-3.0](https://img.shields.io/badge/license-AGPL--3.0-blue)](https://choosealicense.com/licenses/agpl-3.0/)
![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777bb3?logo=php)
![Laravel 12](https://img.shields.io/badge/Laravel-12-ff2d20?logo=laravel)
![Filament 4](https://img.shields.io/badge/Filament-4-0ea5e9)
[![Laravel](https://github.com/crimsonstrife/forge/actions/workflows/laravel.yml/badge.svg?branch=prod)](https://github.com/crimsonstrife/forge/actions/workflows/laravel.yml)

**Forge** is a Laravel-powered project & issue management app for small studios and indie teams. Track projects and issues, define workflows, connect support tickets, and manage admin data via Filament—all with a modern Livewire UI.

> **Note:** This project is **not** affiliated with Laravel Forge (server management).

---

## Features

- **Projects & Issues**
    - Issue types, statuses, priorities; parent/child relations.
    - Comments, attachments (Spatie Media Library), tags, time entries, external links.
    - Issue linking (blocks / relates to / duplicates, etc.).
- **Goals**
    - Link goals to projects and issues; visualize progress.
- **Service Desk**
    - Support tickets that map to issues (staff-only links & indicators).
- **Admin (Filament)**
    - Manage IssueType / IssueStatus / IssuePriority, ProjectType / ProjectStatus, and more.
    - Centralized settings (Spatie Settings).
    - Icon management (built-ins + custom SVG uploads).
- **Permissions**
    - Roles + advanced permission sets (admin vs superadmin), with mute/override behavior.
    - Superadmins access Filament; admins access the Jetstream dashboard.
- **Modern Stack**
    - Folio file-based routing, Jetstream (Livewire), Pennant feature flags, Scout search, Reverb for realtime.

---

## Tech Stack

- **Runtime:** PHP 8.3, MySQL 8+ (or MariaDB 10.6+), Redis (recommended)
- **Framework:** Laravel 12
- **Frontend:** Jetstream v5 (Livewire v3 + Flux v2), Folio v1  
  App UI uses **Bootstrap**; Filament panel uses **TailwindCSS v4**
- **Admin:** Filament v4
- **Other:** Pennant v1, Scout v10, Livewire Volt v1, Reverb v1

> **Node requirement:** Vite 7 needs **Node ≥ 20.19.0** (npm 10+).

---

## Quick Start

1. **Clone & install**

    ```bash
    git clone https://github.com/crimsonstrife/forge.git
    cd forge
    cp .env.example .env
    composer install
    npm install
    php artisan key:generate

    ```

2. **Configure .env**

    ```bash
    APP_URL=http://forge.test          # or http://localhost:8000/your domain
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database
    DB_USERNAME=your_username
    DB_PASSWORD=your_password

    CACHE_DRIVER=redis                  # or: file
    SESSION_DRIVER=redis                # or: file
    QUEUE_CONNECTION=database           # or: redis

    ```

3. **Database & storage**

    ```bash
    php artisan migrate
    php artisan storage:link
    php artisan db:seed

    ```

4. **Frontend**

    ```bash
    npm install        # if you did not run it already
    npm run dev        # or: npm run build

    ```

5. **Run**
    ```bash
    php artisan serve
    # optional:
    # php artisan reverb:start   # realtime
    # php artisan queue:work     # queues
    ```

## Conventions

- **Routing:** Folio file-based pages (resources/views/pages/...)
- **Admin:** Filament v4 resources for reference data & settings
- **ORM:** Eloquent with eager loading to avoid N+1
- **Validation:** Form Request classes
- **Authorization:** Policies & gates
- **Jobs:** Long-running tasks queued (ShouldQueue)

## Testing

```bash
php artisan test
```
