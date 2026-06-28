# DocManager

Self-hosted document management system built with **Laravel 12**.

| App | URL | Who |
|---|---|---|
| Client | `/` | All authenticated users |
| Admin Panel | `/admin` | Admins and Editors |

> **Full documentation** → open `documentation/index.html` in your browser.

---

## Features

### Document Management
- Upload, version, and organise files (PDF, Word, Excel, images, zip, …)
- Category and tag taxonomy with hierarchical categories
- Full-text search — offline TF-IDF index, no external search engine required
- Favorites, reading lists, download history, saved searches
- Soft-delete with configurable trash retention; version history with restore
- Share links with optional password and expiry
- Document ratings and comments

### Real-Time Chat
- WhatsApp-style conversations — private and group chats
- Message types: text, images, files
- Reply-to with inline media preview (thumbnail for images, filename for files)
- Edit and delete messages (broadcast to all participants in real time)
- Typing indicators, online presence dot, read receipts (✓ / ✓✓)
- Mute individual conversations
- Group management: rename, add/remove members, leave group
- In-conversation message search
- **Offline mode**: messages queued to `localStorage` while disconnected, flushed automatically on reconnect; cached messages load instantly from local storage on page open

### Internationalisation (i18n)
- English and Chinese (Simplified) — full translation of all client and admin views
- Language switcher in the top navigation (globe icon)
- Per-user locale preference persisted to the database; falls back to session then app default
- **Offline-safe**: translations are baked into the page HTML (`window.LANG`) at render time — no network request needed to change language

### HR & Productivity
- Employee directory with departments, positions, and linked user accounts
- Work reports (daily / weekly / monthly) with approval workflow
- Attendance tracking with check-in / check-out and admin manual entry
- Project registry
- Science & Technology and Basic Knowledge article libraries
- Tests and quizzes with a question bank, time limits, and scoring

### Administration
- Role-based access: Admin, Editor, Viewer
- Account request flow — users submit requests, admins approve or reject
- Audit log and activity log with CSV export
- Storage management with per-user quotas
- Queue job monitor and failed-job retry
- Backup & restore with optional scheduled backups
- Search index management (rebuild, per-document re-index)
- Document comparison (diff view)
- Admin notifications with optional pin-as-announcement
- Two-factor authentication (TOTP + recovery codes) for all users

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

### Production / no-npm

```bash
# Build assets once (while online)
npm run build

# Remove hot file if present
rm -f public/hot

# Start Laravel and the chat WebSocket server
php artisan serve --host=0.0.0.0 --port=8000
php artisan reverb:start
php artisan queue:work
```

> In production, run `reverb:start` and `queue:work` under a process manager (e.g. Supervisor). Proxy WebSocket upgrades on the Reverb port through your reverse proxy (Nginx example in `documentation/project/index.html`).

> All fonts and assets are self-hosted. No CDN dependencies.

---

## Environment (`.env`)

| Key | Default | Notes |
|---|---|---|
| `DB_CONNECTION` | `mysql` | Change to `sqlite` for zero-config local dev |
| `QUEUE_CONNECTION` | `database` | Use `redis` in production |
| `APP_URL` | `http://localhost` | Set to `http://127.0.0.1:8000` when using `artisan serve` |
| `APP_LOCALE` | `en` | Default locale (`en` or `zh`) |
| `SHARE_LINK_EXPIRY_HOURS` | `24` | Share link lifetime |
| `TRASH_RETENTION_DAYS` | `30` | Days before soft-deleted docs are purged |
| `BROADCAST_CONNECTION` | `reverb` | Chat real-time transport — self-hosted, no third-party broadcaster |
| `REVERB_APP_ID` / `REVERB_APP_KEY` / `REVERB_APP_SECRET` | generated on install | Reverb app credentials — see `php artisan reverb:install` |
| `REVERB_HOST` / `REVERB_PORT` / `REVERB_SCHEME` | `localhost` / `8080` / `http` | Where the Reverb WebSocket server listens; matched by `VITE_REVERB_*` for the browser client |

---

## Internationalisation

Supported locales: **`en`** (English), **`zh`** (Chinese Simplified).

The active locale is resolved in this priority order:
1. `users.locale` column (per-user preference, persisted across sessions)
2. `locale` session key (set by the language switcher)
3. `APP_LOCALE` in `.env`

Users switch language via the globe icon (🌐) in the top navigation. The choice is saved immediately to their account if logged in.

To add a new locale, create `lang/<code>/` with the same file structure as `lang/en/` and add the code to `App\Http\Middleware\SetLocale::SUPPORTED`.

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

## Chat — Offline Behaviour

| Scenario | Behaviour |
|---|---|
| WebSocket drops, HTTP works | Yellow "Reconnecting…" banner; text messages still send via HTTP |
| Fully offline (`navigator.onLine = false`) | Red "Offline" banner; messages queued in `localStorage`; optimistic ⏳ indicator shown |
| Back online | Queued messages flushed in order; banner clears automatically |
| Page reload while offline | Last 100 messages load instantly from `localStorage` cache |
| File/image send while offline | Immediate "Files cannot be sent while offline" error — files are never queued |
| Failed queue flush | Failed items stay as ❌ retry buttons; successfully sent items are removed from queue |

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
