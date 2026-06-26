# DocManager

Self-hosted document management system built with **Laravel 12**.

| App | URL | Who |
|---|---|---|
| Client | `/` | All authenticated users |
| Admin Panel | `/admin` | Admins and Editors |

> **Full documentation** → open `documentation/index.html` in your browser.

---

## Prerequisites

| Requirement | Version |
|---|---|
| PHP | 8.2+ |
| MySQL 8 / SQLite | — |
| Composer | 2.x |
| Node.js / npm | 18+ (build only) |

Enable PHP extensions: `pdo_mysql`, `fileinfo`, `mbstring`, `zip`

---

## Setup

```bash
# Install everything and run migrations in one step
composer run setup
```

---

## Database

### Migrate

```bash
# Run all pending migrations
php artisan migrate

# Fresh install — drop all tables and re-run from scratch
php artisan migrate:fresh

# Check migration status
php artisan migrate:status
```

> **Warning:** `migrate:fresh` drops all data. Dev only.

### Seed

```bash
# Seed starter data (admin account + default categories)
php artisan db:seed

# Refresh database and re-run all seeders
php artisan migrate:fresh --seed

# Delete stale records from the programming problem bank by category
php artisan tinker --execute="App\Models\Problem::where('category', 'Algorithms')->delete()"

# Re-run a specific seeder (problem bank is grouped by category)
php artisan db:seed --class=JavaScriptSeeder
php artisan db:seed --class=MathSeeder
php artisan db:seed --class=AlgorithmsSeeder
php artisan db:seed --class=AISeeder
```

The seeder creates:

| | Value |
|---|---|
| Username | `admin` |
| Password | `Admin@1234` |
| Categories | Engineering, Science, Mathematics, Computer Science, Standards & Codes, Manuals, Reports |

> Change the admin password after first login.

---

## Start

### Dev mode (four processes)

```bash
composer run dev
```

| Process | What it runs |
|---|---|
| `server` | `php artisan serve` on port 8000 |
| `queue` | `php artisan queue:listen` — content extraction + TF-IDF indexing |
| `vite` | `npm run dev` — Tailwind + Alpine HMR |
| `reverb` | `php artisan reverb:start` on port 8080 — real-time chat |

### Offline mode (no npm, no internet)

```bash
# Build assets once (while online)
npm run build

# Remove hot file if present
rm -f public/hot

# Start Laravel and the chat WebSocket server
php artisan serve --host=0.0.0.0 --port=8000
php artisan reverb:start
```

> In production, keep `reverb:start` running under a process manager (e.g. Supervisor) and proxy WebSocket upgrades on the Reverb port through your reverse proxy. Without it, chat falls back to save-only — messages still send, but live delivery/presence won't update until the page is reloaded.

> All fonts and assets are self-hosted. No CDN dependencies.

---

## Environment (`.env`)

| Key | Default | Notes |
|---|---|---|
| `DB_CONNECTION` | `mysql` | Change to `sqlite` for zero-config local dev |
| `QUEUE_CONNECTION` | `database` | Use `redis` in production |
| `APP_URL` | `http://localhost` | Set to `http://127.0.0.1:8000` when using `artisan serve` |
| `SHARE_LINK_EXPIRY_HOURS` | `24` | Share link lifetime |
| `TRASH_RETENTION_DAYS` | `30` | Days before soft-deleted docs are purged |
| `BROADCAST_CONNECTION` | `reverb` | Chat real-time transport — self-hosted, no third-party broadcaster |
| `REVERB_APP_ID` / `REVERB_APP_KEY` / `REVERB_APP_SECRET` | generated on install | Reverb app credentials — see `php artisan reverb:install` |
| `REVERB_HOST` / `REVERB_PORT` / `REVERB_SCHEME` | `localhost` / `8080` / `http` | Where the Reverb WebSocket server listens; matched by `VITE_REVERB_*` for the browser client |

---

## Key Artisan Commands

```bash
# Search index
php artisan search:reindex          # Re-extract text from all documents (queued)
php artisan search:build-tfidf      # Rebuild TF-IDF semantic index (in-process)

# Maintenance
php artisan dms:prune-trash         # Permanently delete old soft-deleted docs
php artisan dms:archive-expired     # Archive docs past their expiry date
php artisan dms:prune-shares        # Delete expired share links
php artisan dms:prune-tokens        # Delete expired Sanctum tokens

# Real-time chat
php artisan reverb:start            # Start the WebSocket server (required for live chat delivery)
```

All maintenance commands accept `--dry-run` to preview what would change without modifying anything.

---

## Offline Documentation

All stack references are available offline — no internet needed:

```
documentation/index.html           ← open this
├── project/index.html             # Full project docs (folder structure, models, routes, features, API)
├── laravel/index.html             # Laravel 12.x — 103 pages
├── tailwindcss/index.html         # Tailwind CSS v3
├── alpinejs/index.html            # Alpine.js v3
├── flowbite/index.html            # Flowbite v2
└── vite/index.html                # Vite v7
```

---

## Tech Stack

Laravel 12 · PHP 8.2 · MySQL 8 · Tailwind CSS 3 · Alpine.js 3 · Vite 7 · Flowbite 2 · Laravel Sanctum · Laravel Reverb (self-hosted WebSockets) · Laravel Echo / pusher-js (client) · TF-IDF semantic search (pure PHP, fully offline)

---

## Tests

```bash
php artisan test
```

Feature tests cover: Search (keyword, AI, hybrid), Favorites, Bookmarks, ReadingLists, Ratings, Notifications.
