# DocManager

A full-featured, self-hosted **document management system** built with **Laravel 12**. Designed for organizations that need a private, offline-capable alternative to cloud DMS platforms — no internet dependency, no third-party services required.

| App | URL | Who |
|---|---|---|
| Client Web App | `/` | All authenticated users |
| Admin Panel | `/admin` | Admins and Editors |

> **Full documentation** → open `documentation/index.html` in your browser.

---

## Project Overview

### What It Does

DocManager lets teams upload, organize, search, and share documents from a single self-hosted platform. All processing — file parsing, indexing, and semantic search — runs on-premise with no cloud dependency.

### User Roles

| Role | Access | Capabilities |
|---|---|---|
| **Admin** | Client + Admin panel | Full control: users, settings, audit logs, storage quotas |
| **Editor** | Client + Admin panel | Manage documents, categories, tags, approve/reject uploads |
| **Viewer** | Client only | Upload, search, download, organize personal library |

### Core Features

**Document Handling**
- Chunked upload for large files with automatic text extraction (PDF, Word, Excel, PowerPoint)
- Document status workflow: draft → pending → published (with approve/reject)
- Version history — every revision is tracked and restorable
- Document locking to prevent concurrent edits
- Soft delete with configurable trash retention; permanent purge via scheduled command
- Document expiry with auto-archive

**Search**
- Keyword search — fast full-text match against extracted content
- TF-IDF semantic search — finds conceptually related documents; runs 100% offline in pure PHP
- Autocomplete suggestions, saved searches, search history

**Personal Library**
- Favorites, Bookmarks (with notes), Reading Lists, Recently Viewed, Download History
- Document ratings and written reviews

**Sharing & Access Control**
- Shareable public links with configurable expiry (`SHARE_LINK_EXPIRY_HOURS`)
- Per-user storage quotas with enforcement on upload
- Account request flow — new users request access, admins approve or reject

**Real-Time Chat & Messaging**
- 1:1 private conversations between any two active users (Admin ↔ Client, Client ↔ Client)
- True real-time delivery via **Laravel Reverb** — a self-hosted WebSocket server (no Pusher/third-party service)
- Online/offline presence, message timestamps, sent/delivered/read indicators
- Lightweight emoji picker, conversation + message search, live unread badges in both the client and admin nav
- Broadcasts degrade gracefully — if the WebSocket server is briefly unavailable, messages still save; only the live push is skipped

**Programming Problems & Developer Assessments**
- Curated bank of coding problems (JavaScript, Math, Algorithms, AI) with reference solutions, browsable in an in-browser code explorer
- **Developer Tests** — build a timed test from existing problems or add one-off custom problems inline, generate a no-account-needed invite link, and grade submissions manually against the reference solution
- Invite links are public but token-scoped and rate-limited; an abandoned in-progress test auto-finalizes on expiry

**Observability**
- Audit log (admin actions) and Activity log (user actions) — both exportable
- Download tracking per document
- Search analytics log
- Background job monitor (queue health)
- Storage usage dashboard

**HR & People Management**
- **Employees** — profiles with photo, department, position, manager hierarchy, employment status, and salary
- **Departments** and **Positions** with headcount tracking
- **Employee Documents** — store contracts, IDs, and certificates per employee

**Attendance**
- Daily check-in/check-out records with work hours calculation
- **Leave Management** — leave requests with type, date range, and approval workflow
- Admin attendance dashboard and employee-level history

**Work Reports**
- Employees submit daily, weekly, or monthly reports with task breakdowns and time tracking
- Manager review workflow: draft → submitted → under review → approved / rejected
- **Team tab** — managers see all direct reports' submitted reports in one view
- Threaded comments (with feedback and revision-request types), file attachments
- Analytics dashboard for admins (submission trends, approval rates)

**Plans**
- Full plan lifecycle: draft → pending → in progress → on hold → completed → cancelled → archived
- Categories: daily, weekly, monthly, quarterly, annual, personal, team, project, strategic
- Priority levels: low, medium, high, critical
- Per-plan task list with assignment, priority, due dates, and completion toggle
- Progress bar auto-calculated from completed / total tasks ratio
- Assign multiple employees via many-to-many (`plan_employees` pivot)
- Comments and file attachments per plan
- Duplicate and archive actions; auto-generated plan numbers (PLN-00001)
- **Client "My Plans"** — employees see only plans they are assigned to; read-only with comment posting

**REST API**
- Token-based auth via Laravel Sanctum
- Endpoints: list/get/create/update/delete documents, download, share, search

**Offline Documentation Hub**
- Bundled offline reference docs for the full stack — no internet needed during development

### Architecture at a Glance

```
┌─────────────────────────────────────────────────────┐
│  Browser (Tailwind CSS 3 + Alpine.js 3 + Flowbite)  │
└──────┬───────────────────────────────────────┬──────┘
       │ HTTP                                  │ WebSocket (Echo/pusher-js)
┌──────▼────────────────────────────────┐  ┌────▼──────────────────────┐
│  Laravel 12 (PHP 8.2+)                │  │  Laravel Reverb            │
│  ├── Client web routes  (/*)          │◄─┤  Self-hosted WS server     │
│  ├── Admin panel routes (/admin/*)    │  │  (chat + presence channels)│
│  └── REST API routes    (/api/*)      │  └────────────────────────────┘
└──────┬──────────────┬──────────────┬──┘
       │              │              │
┌──────▼──────┐ ┌─────▼──────┐ ┌───▼──────────────────┐
│  MySQL 8    │ │  Queue     │ │  Local File Storage   │
│  (or SQLite)│ │  Worker    │ │  (disk / configurable)│
└─────────────┘ │  - Extract │ └──────────────────────┘
                │  - TF-IDF  │
                └────────────┘
```

**Key database tables:** `resources`, `document_versions`, `categories`, `tags`, `users`, `shares`, `resource_embeddings` (TF-IDF vectors), `audit_logs`, `activity_logs`, `download_logs`, `search_logs`, `problems`, `tests` / `test_problems` / `test_invites` / `test_answers` (developer assessments), `conversations` / `messages` / `conversation_reads` (chat), `departments` / `positions` / `employees` / `employee_documents`, `attendances` / `attendance_leaves`, `work_reports` / `work_report_tasks` / `work_report_comments` / `work_report_attachments`, `plans` / `plan_employees` / `plan_tasks` / `plan_comments` / `plan_attachments`

### Design Principles
- **Offline-first** — no S3, no Elasticsearch, no external AI service
- **Self-contained** — single server, standard LAMP/LEMP stack
- **Observable** — every significant action is logged and queryable by admins

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
