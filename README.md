# DocManager — Document Management System

A **secure, full-featured document management system** built with **Laravel 12** (PHP 8.2+). Supports dual-mode search (keyword + offline TF-IDF semantic), document versioning, lock/unlock enforcement, role-based access control, a REST API, and a complete offline documentation hub — no cloud dependency.

The system ships as **two web applications** sharing one Laravel backend and database:

| App | URL prefix | Who uses it |
|---|---|---|
| **Admin Panel** | `/admin` | Admins and Editors — full document and user management |
| **Client Web App** | `/` | All authenticated users — browse, search, preview, download |

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Quick Setup](#quick-setup)
3. [Environment Variables](#environment-variables)
4. [Starting the Dev Stack](#starting-the-dev-stack)
5. [Offline / Production Mode](#offline--production-mode)
6. [Folder Structure](#folder-structure)
7. [Application Architecture](#application-architecture)
8. [Database Schema](#database-schema)
9. [Features — Admin Panel](#features--admin-panel)
10. [Features — Client Web App](#features--client-web-app)
11. [Search System](#search-system)
12. [File Upload & Storage](#file-upload--storage)
13. [Content Extraction](#content-extraction)
14. [Document Preview](#document-preview)
15. [Background Jobs & Queue](#background-jobs--queue)
16. [Artisan Commands Reference](#artisan-commands-reference)
17. [Offline Documentation Hub](#offline-documentation-hub)
18. [REST API](#rest-api)
19. [Security Architecture](#security-architecture)
20. [Role Permissions Matrix](#role-permissions-matrix)
21. [Tech Stack](#tech-stack)
22. [System Architecture Diagram](#system-architecture-diagram)

---

## Prerequisites

| Requirement | Version | Notes |
|---|---|---|
| PHP | 8.2+ | Required extensions: `pdo_mysql`, `fileinfo`, `mbstring`, `openssl`, `zip`, `gd` |
| MySQL | 8.0+ | FULLTEXT search requires MySQL 8 (SQLite for local dev is fine) |
| Composer | 2.x | PHP dependency manager |
| Node.js / npm | 18+ | Required for building frontend assets only |

### Enable required PHP extensions

Locate your `php.ini` (`php --ini`) and uncomment:

```ini
extension=fileinfo
extension=pdo_mysql
extension=zip
extension=gd
```

Verify they loaded:

```bash
php -r "var_dump(extension_loaded('fileinfo'), extension_loaded('pdo_mysql'), extension_loaded('zip'));"
```

---

## Quick Setup

### One-command setup (recommended)

```bash
composer run setup
```

This single script: installs PHP dependencies, copies `.env.example → .env`, generates the app key, runs all migrations, installs npm packages, and builds frontend assets.

### Manual step-by-step

```bash
# 1. Install PHP dependencies
composer install

# 2. Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# 3. Configure database in .env (see Environment Variables section)

# 4. Run migrations
php artisan migrate

# 5. Install and build frontend
npm install
npm run build

# 6. Create the storage symlink (for public avatars)
php artisan storage:link

# 7. Create your first admin account
php artisan tinker
>>> App\Models\User::create([
    'name' => 'Admin',
    'username' => 'admin',
    'password' => bcrypt('your-password'),
    'role' => 'admin',
    'status' => 'active',
]);
```

---

## Environment Variables

Edit `.env` after copying from `.env.example`.

### Database

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=docmanager
DB_USERNAME=root
DB_PASSWORD=
```

> For local development with SQLite: `DB_CONNECTION=sqlite` (no other DB vars needed)

### Application

| Variable | Default | Description |
|---|---|---|
| `APP_NAME` | `DocManager` | Shown in the browser tab and nav |
| `APP_ENV` | `local` | `production` disables debug mode |
| `APP_DEBUG` | `true` | Set `false` in production |
| `APP_KEY` | _(generated)_ | 32-char encryption key — never share |
| `APP_URL` | `http://localhost` | Full base URL |

### Queue & Cache

| Variable | Default | Production recommendation |
|---|---|---|
| `QUEUE_CONNECTION` | `database` | `redis` |
| `CACHE_STORE` | `file` | `redis` |
| `SESSION_DRIVER` | `file` | `database` or `redis` |

### Application-specific

| Variable | Default | Description |
|---|---|---|
| `SHARE_LINK_EXPIRY_HOURS` | `24` | Default share link lifetime |
| `TRASH_RETENTION_DAYS` | `30` | Days before soft-deleted documents are permanently purged |
| `ACCOUNT_LOCKOUT_ATTEMPTS` | `5` | Failed logins before account lock |
| `ACCOUNT_LOCKOUT_MINUTES` | `15` | Duration of account lockout |
| `MAX_UPLOAD_SIZE_MB` | `50` | Per-file upload size limit |

---

## Starting the Dev Stack

### Full dev mode (four processes via `concurrently`)

```bash
composer run dev
```

| Process | Command | Purpose |
|---|---|---|
| `server` | `php artisan serve` | Laravel on port 8000 |
| `queue` | `php artisan queue:listen --tries=1` | Processes content extraction and TF-IDF indexing jobs |
| `logs` | `php artisan pail` | Real-time log streaming in the terminal |
| `vite` | `npm run dev` | Tailwind CSS + Alpine.js with HMR |

App available at **http://127.0.0.1:8000**

### Windows: fix Vite IPv6 mismatch

On Windows, Vite defaults to `[::1]` (IPv6) which breaks asset loading. Add `host` to `vite.config.js`:

```js
server: {
    host: '127.0.0.1',
},
```

---

## Offline / Production Mode

Run with no Vite dev server — assets served from pre-built files.

```bash
# 1. Build and compile all CSS/JS (do once, while online)
npm run build

# 2. Remove the hot file (if present) so Laravel uses public/build/
Remove-Item public\hot -ErrorAction SilentlyContinue   # PowerShell
rm -f public/hot                                        # bash

# 3. Start Laravel only
php artisan serve --host=127.0.0.1 --port=8000
```

| | Dev mode | Offline mode |
|---|---|---|
| Assets from | Vite dev server (`:5173`) | `public/build/` |
| Servers needed | Laravel + Vite | Laravel only |
| `public/hot` file | present | deleted |
| HMR | yes | no (rebuild manually) |

### Make the repo permanently offline-ready

Remove from `.gitignore` and commit both folders:

```
/vendor
/public/build
```

With `vendor/` and `public/build/` committed, no internet, Composer, or npm is needed on any machine.

---

## Folder Structure

```
docmanager/
├── app/
│   ├── Console/Commands/       # 8 custom Artisan commands
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # 14 admin panel controllers
│   │   │   ├── Api/            # 3 REST API controllers (Sanctum)
│   │   │   ├── Auth/           # 4 auth flow controllers (Breeze)
│   │   │   └── Client/         # 15 user-facing controllers
│   │   ├── Middleware/         # RequireRole, RequireActiveAccount, SecurityHeaders
│   │   ├── Requests/           # Form request validators
│   │   └── Resources/          # API resource transformers (JSON)
│   ├── Jobs/                   # ExtractDocumentContent, IndexDocumentTfidf
│   ├── Models/                 # 24 Eloquent models
│   ├── Providers/              # AppServiceProvider
│   ├── Services/               # ContentExtractorService, TfidfService
│   └── View/Components/        # AppLayout, GuestLayout
├── bootstrap/
│   ├── app.php                 # Application instance, middleware aliases
│   ├── providers.php           # Auto-discovered providers
│   └── cache/                  # Cached packages and service bindings
├── config/                     # app, auth, database, filesystems, queue, cache, etc.
├── database/
│   ├── migrations/             # 31 migration files
│   ├── factories/              # Faker model factories
│   └── seeders/
├── documentation/              # Offline HTML reference docs (self-contained)
│   ├── index.html              # Hub — links to all doc sets
│   ├── project/index.html      # This project's full documentation
│   ├── laravel/index.html      # Laravel 12.x — all 103 pages offline (4 MB)
│   ├── tailwindcss/index.html  # Tailwind CSS v3
│   ├── alpinejs/index.html     # Alpine.js v3
│   ├── flowbite/index.html     # Flowbite v2
│   └── vite/index.html         # Vite v7
├── public/                     # Web root — only this folder is exposed by the server
│   ├── index.php               # Laravel front controller
│   ├── storage/                # Symlink → storage/app/public/ (php artisan storage:link)
│   ├── vendor/pdfjs/           # PDF.js worker (bundled locally, no CDN)
│   └── build/                  # Vite compiled assets (CSS + JS)
├── resources/
│   ├── css/app.css             # Tailwind directives
│   ├── js/app.js               # Alpine.js bootstrap
│   └── views/                  # 54 Blade templates
│       ├── layouts/            # app.blade.php, admin.blade.php, guest.blade.php
│       ├── admin/              # 16 admin panel views
│       ├── documents/          # show.blade.php, preview.blade.php
│       ├── home/               # index.blade.php (doc grid + category drawer)
│       ├── search/             # index.blade.php (search + filter drawer)
│       └── ...                 # auth, profile, favorites, reading-lists, etc.
├── routes/
│   ├── web.php                 # Browser routes (session auth, CSRF)
│   ├── auth.php                # Login / register / password routes
│   ├── api.php                 # REST API (/api prefix, Sanctum token auth)
│   └── console.php             # Scheduled Artisan task definitions
├── storage/
│   ├── app/
│   │   ├── chunks/             # Temporary chunks for in-progress large file uploads
│   │   ├── private/
│   │   │   ├── resources/      # Uploaded documents (UUID-named, never publicly accessible)
│   │   │   ├── documents/      # Offline doc markdown files (laravel-docs/12.x/)
│   │   │   └── search/
│   │   │       └── tfidf_idf.json  # TF-IDF IDF dictionary (rebuilt by search:build-tfidf)
│   │   ├── public/
│   │   │   └── avatars/        # User profile pictures (accessible via /storage symlink)
│   │   └── temp/               # Bulk ZIP downloads, chunk assembly workspace
│   ├── framework/              # Compiled Blade views, sessions, file cache
│   └── logs/laravel.log
├── tests/                      # PHPUnit test suites
├── vendor/                     # Composer packages (generated — never edit)
├── .env                        # Local environment (never commit)
├── .env.example                # Environment template
├── artisan                     # Laravel CLI
├── composer.json               # PHP dependencies
├── package.json                # Node dependencies
├── tailwind.config.js
├── vite.config.js
```

> **Storage disk root:** The `local` disk in Laravel 11+ resolves to `storage/app/private/`. When using `Storage::disk('local')->put('resources/file.pdf', …)` the physical path is `storage/app/private/resources/file.pdf`. Never prefix paths with `private/` when using the `local` disk driver.

---

## Application Architecture

```
Browser
   │
   ├── GET /admin/*  ─────────────────────────────────────────────────────────────────┐
   │                                                                                   │
   └── GET /*, /api/*  ──────────────────────────────────────────────────────────┐    │
                                                                                  │    │
                                                              Client Web App      │    Admin Panel
                                                              (Blade + Tailwind)  │    (Blade + Tailwind)
                                                                                  │    │
                                                           ┌──────────────────────┘    │
                                                           │         Laravel 12        │
                                                           │  ─────────────────────────┘
                                                           │
                                                           ├── Middleware: auth, active, role:admin,editor
                                                           ├── Router (routes/web.php + api.php)
                                                           ├── Controllers
                                                           ├── Eloquent ORM (24 models)
                                                           ├── Services: ContentExtractorService, TfidfService
                                                           ├── Queue jobs: ExtractDocumentContent, IndexDocumentTfidf
                                                           └── Scheduler: prune trash/shares/tokens, archive expired
                                                                          │
                                                               ─────────────────────────────────
                                                               MySQL / SQLite  │  Local storage
                                                               (31 tables)     │  storage/app/private/
```

---

## Database Schema

### Tables reference

| Table | Purpose |
|---|---|
| `users` | Accounts — role (`admin`/`editor`/`viewer`), status (`pending`/`active`/`inactive`), storage_quota_mb, failed_login_attempts, locked_until |
| `personal_access_tokens` | Sanctum API tokens |
| `resources` | Documents — title, file_path, file_type (MIME), content (extracted text), status, locked_by, locked_at, expires_at; soft-deletable |
| `document_versions` | Version history per document with file_path and SHA256 hash |
| `document_access_logs` | Per-document log of who viewed / downloaded each version and when |
| `categories` | Hierarchical category tree (parent_id self-join, slug) |
| `tags` | Tag definitions (name, slug) |
| `resource_tags` | Many-to-many pivot: resources ↔ tags |
| `favorites` | Per-user saved documents |
| `recently_viewed` | Auto-tracked per-user view history (updated_at timestamp) |
| `saved_searches` | Named saved search query + filter JSON per user |
| `reading_lists` | User-created named document collections (public/private flag) |
| `reading_list_items` | Pivot: reading list ↔ document with sort order |
| `bookmarks` | In-document page bookmarks (page number + optional label) |
| `document_ratings` | 1–5 star ratings + optional text review, one per user per document |
| `shares` | Signed time-limited share tokens (token, expires_at, revoked_at) |
| `notifications` | Per-user in-app notifications (type, title, message, is_read) |
| `user_preferences` | View mode, items per page, notification opt-in toggles per user |
| `account_requests` | Username-change and account-deletion requests with admin approval flow |
| `settings` | Global application config key-value store (cached) |
| `audit_logs` | Admin action trail — who did what to which resource, with IP |
| `activity_logs` | User session events — login, logout, failed access, lockouts, with IP + user agent |
| `search_logs` | Every search query — text, type (keyword/ai), results_count, user |
| `download_logs` | Per-file download records — user, resource, IP, timestamp |
| `resource_embeddings` | TF-IDF sparse vectors per document (chunk_index, embedding JSON, model=tfidf-v1) |
| `jobs` | Laravel queue jobs table |
| `cache` | Laravel file/database cache entries |

---

## Features — Admin Panel

### Document Management

- Upload documents with real-time progress (Alpine.js + `XMLHttpRequest`)
- **Chunked upload** for large files over 50 MB (avoids PHP `upload_max_filesize` limits) — browser splits file into chunks, server assembles and validates hash
- Edit title, description, category, tags
- **Soft delete** — moves to Trash (`deleted_at` timestamp); restorable
- **Force delete** — permanently removes DB record and file from disk
- **Bulk operations**: approve, trash, reject, assign category, download as ZIP (`ZipArchive`)
- **Document locking** — lock a document to block all downloads and previews until unlocked; locked_by and locked_at recorded; admins can force-unlock any document
- **Approval workflow** — Draft → Pending Review → Published → Archived; rejection returns to Draft; only Published documents are visible to Viewers
- **Document expiry** — set an `expires_at` date; the `dms:archive-expired` scheduler automatically moves the document to Archived status
- **Restore from Trash** — `PATCH /admin/documents/{id}/restore` (uses `->withTrashed()` route binding)
- Access log per document — full history of who viewed or downloaded each version

### Version Control

- Upload a new version of any document
- Roll back to any previous version
- Full version history with uploader, timestamp, and change notes
- SHA256 hash per version for integrity verification

### User Management _(admin only)_

- Create admin and editor accounts directly (no self-registration for staff)
- Review self-registered client accounts in the Pending queue — activate or reject with a written reason
- Bulk activate / bulk reject pending registrations
- Dashboard badge shows count of new pending registrations
- Edit any user's profile (name, role, status)
- Reset any user's password directly (no email required)
- Activate / deactivate accounts
- Per-user storage quota (enforced on upload via `User::wouldExceedQuota()`)
- Process account deletion requests and username-change requests

### Analytics & Logs

- **Dashboard**: stat cards (total docs, storage used, downloads today, active users, failed jobs); upload trends; top downloaded files; popular search terms
- **Audit logs** — full admin action trail with IP, user, resource, timestamp — filterable, exportable to CSV
- **Activity logs** — session events (login, logout, failed access, lockouts) with IP and user agent — exportable to CSV
- **Queue monitor** — view pending and failed background jobs, retry individual or all failed jobs

### Other Admin Features

- Hierarchical category CRUD (parent/child tree)
- Tag CRUD + **tag merge** (moves all document associations from source tag to target, deletes source)
- Broadcast system notifications to all active users at once
- Search index management — trigger `search:reindex` (re-extract text), `search:build-tfidf` (rebuild TF-IDF index)
- Storage usage breakdown by user and by file type
- Global settings page (upload limits, session timeout, share link expiry)

---

## Features — Client Web App

### Authentication

- Self-registration with **username + password only** (no email required)
- New accounts start as **Pending** — cannot log in until an admin activates them
- Pending users see an "awaiting activation" message
- Account lockout after N failed logins (configurable)
- Session-based auth (Laravel Breeze); remember-me tokens rotated on each use
- Username change and account deletion go through an **admin-approval request** flow (users cannot change username directly)

### Browse & Discover

- Document grid with collapsible **category tree sidebar** (desktop) + slide-in **category drawer** (mobile)
- Sort by: name, upload date, file size, file type, most downloaded
- Pagination on all document lists
- Breadcrumb navigation for category drill-down
- Related documents panel on Document Detail page

### Search _(see [Search System](#search-system))_

- **Keyword mode** (MySQL FULLTEXT) and **AI Semantic mode** (TF-IDF) — togglable per query
- Filters: file type, date range, category
- Saved searches, sort options, match-score badges on AI results

### Document Actions

- **Preview** PDF (inline via `<iframe>`) and images (inline via `<img>`) — blocked when document is locked
- **Download** — controller-gated, permission-checked, lock-checked
- **Streaming** — `GET /documents/{id}/stream` serves file inline (`Content-Disposition: inline`) for the preview iframe
- Generate and copy a signed share link
- Rate document 1–5 stars + optional text review
- Save in-document page bookmark with optional label

### Personal Features

| Feature | URL | Storage |
|---|---|---|
| Favorites | `/favorites` | `favorites` table |
| Recently Viewed | `/history` | `recently_viewed` table |
| Download History | `/downloads` | `download_logs` table |
| Saved Searches | `/saved-searches` | `saved_searches` table |
| Reading Lists | `/reading-lists` | `reading_lists` + `reading_list_items` |
| Notifications | `/notifications` | `notifications` table |
| Profile | `/profile` | `users` + `user_preferences` |

---

## Search System

### 1. Keyword Search (MySQL FULLTEXT)

- `MATCH(title, description, content) AGAINST(? IN BOOLEAN MODE)`
- Supports boolean operators: `+required -excluded "exact phrase"`
- Results sorted by MySQL relevance score
- Fast, exact — best for known terminology

### 2. TF-IDF Semantic Search ("AI Search")

Pure PHP implementation — no external API, no vector database, no internet required.

**Build phase** (`search:build-tfidf` command):

```
Pass 1 — scan all documents, count document frequency (DF) for every unique term
       → build IDF dictionary: log((N+1)/(df+1)) + 1  (smooth IDF)
       → save to storage/app/private/search/tfidf_idf.json

Pass 2 — compute TF-IDF vector for each document
       → L2-normalize each vector
       → store in resource_embeddings (model=tfidf-v1, chunk_index=0)
```

**Query phase** (user submits "AI Search"):

```
Tokenize query text
→ vectorize using stored IDF dictionary
→ cosine similarity against every stored embedding (dot product of L2-normalised vectors)
→ sort by score, paginate, return results with "% match" badge
```

**Indexed text**: `title + description + content` (content = text extracted from the file)

**Algorithm details**:

| Step | Detail |
|---|---|
| Tokenize | lowercase → strip punctuation → remove 50+ English stop words → filter tokens < 3 chars |
| TF | term count ÷ total tokens per document |
| IDF | `log((N+1) / (df+1)) + 1` (smooth — avoids zero-division, handles unseen terms) |
| TF-IDF | TF × IDF, then L2-normalized |
| Cosine similarity | dot product over query vector terms only (sparse, efficient) |

### Search filters

| Filter | Values |
|---|---|
| File type | `application/pdf`, `image/png`, `application/vnd.openxmlformats…`, etc. |
| Upload date range | from / to |
| Category | slug or ID |

### Sort options

| Sort key | Available in |
|---|---|
| Relevance | keyword + AI modes |
| Upload date (asc/desc) | all modes |
| File name (asc/desc) | all modes |
| File size (asc/desc) | all modes |
| Download count (desc) | all modes |

---

## File Upload & Storage

### Standard upload

1. Validate (file required, MIME type allowlist, max size)
2. Generate UUID filename — stored at `storage/app/private/resources/{uuid}.ext`
3. Create `Resource` record with MD5 `file_hash` for deduplication detection
4. Dispatch `ExtractDocumentContent` job to queue

### Chunked upload (large files)

1. Browser splits file into N slices
2. `POST /admin/documents/upload/chunk` — each slice stored in `storage/app/chunks/{upload_id}/`
3. `POST /admin/documents/upload/assemble` — concatenates slices in order, verifies hash, moves to `resources/{uuid}.ext`, creates Resource, dispatches extraction job

### Supported types

| Format | MIME type | Content extraction |
|---|---|---|
| PDF | `application/pdf` | ✔ smalot/pdfparser |
| DOCX | `application/vnd.openxmlformats-officedocument.wordprocessingml.document` | ✔ phpoffice/phpword |
| XLSX / XLS | `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet` | ✔ phpoffice/phpspreadsheet |
| PPTX | `application/vnd.openxmlformats-officedocument.presentationml.presentation` | ✔ phpoffice/phppresentation |
| TXT / MD | `text/plain` | ✔ native PHP |
| Images (JPG, PNG, GIF, WEBP) | `image/*` | ✗ (indexed by title + description) |
| ZIP, other | various | ✗ (indexed by title + description) |

> **File type storage note:** `resources.file_type` stores the full MIME type (e.g. `application/pdf`), not a short extension. All type checks in the codebase use MIME types, not extensions.

---

## Content Extraction

Handled by `app/Services/ContentExtractorService.php`:

| Method | Library | Handles |
|---|---|---|
| `extractPdf()` | smalot/pdfparser | Text layer of PDF files |
| `extractDocx()` | phpoffice/phpword | Body text of Word documents |
| `extractXlsx()` | phpoffice/phpspreadsheet | All sheets, all rows, all cells |
| `extractPptx()` | phpoffice/phppresentation | All slides, all shapes, all paragraph runs |
| `extractText()` | PHP built-in | Plain text files (.txt, .md) |
| `clean()` | — | Normalise whitespace, strip control characters, truncate at 5 MB |

Extracted text is stored in `resources.content (LONGTEXT)`.

---

## Document Preview

The preview system uses an in-process streaming route — no third-party preview service needed.

| File type | Preview method |
|---|---|
| `application/pdf` | `<iframe src="/documents/{id}/stream">` — stream route serves file inline |
| `image/*` | `<img src="/documents/{id}/stream">` — stream route serves file inline |
| All other types | No preview — download only |

### Stream route

`GET /documents/{id}/stream` → `DocumentController@stream`

- Checks `isPublished()` → 404 if not published
- Checks `isLocked()` → 403 if locked
- Validates MIME type is PDF or image → 404 otherwise
- Checks file exists on disk → 404 if missing
- Returns `response()->file()` with `Content-Type` header (inline, not attachment)

### Lock enforcement

When `isLocked()` is true:

| Point | Behaviour |
|---|---|
| Download button | Replaced with grey "Locked" badge (no link) |
| "Open Preview" button | Hidden |
| `download` route | Redirects back with error flash |
| `stream` route | Returns HTTP 403 |
| `preview` route | Returns HTTP 403 |
| Document detail page | Shows yellow "Document locked by [name]" banner |

---

## Background Jobs & Queue

Start the queue worker (required for uploads to become searchable):

```bash
# Development — re-starts on failure
php artisan queue:listen --tries=1

# Production — daemonise with Supervisor
php artisan queue:work --daemon
```

### ExtractDocumentContent

| Property | Value |
|---|---|
| Triggered by | Document upload / `search:reindex` command |
| Retries | 3 |
| Timeout | 120 seconds |
| What it does | Calls `ContentExtractorService::extract()`, stores result in `resources.content`, dispatches `IndexDocumentTfidf` |

### IndexDocumentTfidf

| Property | Value |
|---|---|
| Triggered by | After `ExtractDocumentContent` completes; also by `search:build-tfidf` |
| Retries | 3 |
| Timeout | 60 seconds |
| What it does | Combines title+description+content, computes TF-IDF vector via `TfidfService`, stores in `resource_embeddings` |
| Guard | Silently skips if IDF dictionary hasn't been built yet |

Monitor failed jobs at `/admin/jobs`. Retry all failed jobs with one click.

---

## Artisan Commands Reference

### Search commands

| Command | Options | Description |
|---|---|---|
| `php artisan search:reindex` | `--chunk=50` | Queue `ExtractDocumentContent` for every non-deleted document. Run after adding new file type support. |
| `php artisan search:build-tfidf` | `--chunk=200` | 2-pass in-process rebuild: compute IDF dictionary (Pass 1) → compute and store TF-IDF vectors for all documents (Pass 2). Runs synchronously (no queue). |

### Maintenance commands

| Command | Options | Schedule | Description |
|---|---|---|---|
| `php artisan dms:prune-trash` | `--dry-run` | Daily 02:00 | Permanently deletes documents soft-deleted longer than `TRASH_RETENTION_DAYS`. Removes DB record and file from disk. |
| `php artisan dms:prune-shares` | `--dry-run` | Daily 02:15 | Deletes share tokens expired more than 7 days ago. |
| `php artisan dms:prune-tokens` | — | Daily 02:30 | Deletes expired Sanctum personal access tokens. |
| `php artisan dms:archive-expired` | `--dry-run` | Hourly | Sets status → `archived` for documents whose `expires_at` has passed. Writes AuditLog entry. |

### Documentation commands

| Command | Options | Description |
|---|---|---|
| `php artisan docs:import-laravel` | `--branch=12.x` `--force` | Downloads the Laravel documentation ZIP from GitHub, extracts 103 markdown files, creates Resource records in the database (category "Laravel 12.x Documentation"), and rebuilds the TF-IDF index. |
| `php artisan docs:build-laravel-offline` | `--branch=12.x` | Reads markdown from `storage/app/private/documents/laravel-docs/12.x/`, converts to HTML with a custom parser, and writes a single self-contained offline HTML file to `documentation/laravel/index.html`. |

### Scheduler setup (production)

Add to cron (`crontab -e`):

```cron
* * * * * cd /var/www/docmanager && php artisan schedule:run >> /dev/null 2>&1
```

---

## Offline Documentation Hub

All project dependencies are available offline as self-contained HTML files:

```
Open: documentation/index.html
```

| Doc set | File | Contents |
|---|---|---|
| **Project Docs** | `documentation/project/index.html` | Full DocManager reference — folder structure, models, controllers, routes, all features |
| **Laravel 12.x** | `documentation/laravel/index.html` | All 103 official docs pages (4,151 KB, single file) |
| **Tailwind CSS v3** | `documentation/tailwindcss/index.html` | Full utility class reference |
| **Alpine.js v3** | `documentation/alpinejs/index.html` | Directives, magic properties, plugins |
| **Flowbite v2** | `documentation/flowbite/index.html` | 56+ component recipes |
| **Vite v7** | `documentation/vite/index.html` | Config reference, plugins, HMR |

### Rebuild the Laravel offline doc

```bash
# Import latest docs from GitHub (downloads ~840 KB ZIP)
php artisan docs:import-laravel --branch=12.x --force

# Regenerate the single-file offline HTML
php artisan docs:build-laravel-offline --branch=12.x
```

---

## REST API

Authentication: **Laravel Sanctum** (Bearer token).

### Obtain a token

```http
POST /api/auth/login
Content-Type: application/json

{ "email": "user@example.com", "password": "secret" }
```

Response: `{ "token": "1|abc…", "user": { … } }`

Use token in subsequent requests: `Authorization: Bearer 1|abc…`

### Endpoints

| Method | URI | Auth | Description |
|---|---|---|---|
| `POST` | `/api/auth/login` | none | Issue Sanctum token (rate-limited: 5/min per IP) |
| `POST` | `/api/auth/logout` | token | Revoke current token |
| `GET` | `/api/auth/me` | token | Current user info |
| `GET` | `/api/resources` | token | List published documents (paginated) |
| `GET` | `/api/resources/{id}` | token | Single document detail |
| `POST` | `/api/resources` | token | Upload a document |
| `PUT` | `/api/resources/{id}` | token | Update document metadata |
| `DELETE` | `/api/resources/{id}` | token | Soft-delete a document |
| `GET` | `/api/resources/{id}/download` | token | Download file (rate-limited: 10/min per user) |
| `POST` | `/api/resources/{id}/share` | token | Generate a signed share link |
| `GET` | `/api/search` | token | Keyword search — logs query to `search_logs` |

### Response format

All document responses use `DocumentResource` / `DocumentResource::collection()` transformers:

- **Included**: `id`, `title`, `description`, `file_type`, `file_size`, `status`, `download_count`, `category`, `tags`, `created_at`
- **Excluded**: `file_path`, `file_hash` (internal fields never exposed via API)
- **Collections** wrapped in `{ data: [...], meta: { current_page, last_page, total, per_page }, links: { first, last, prev, next } }`

---

## Security Architecture

### File access

- Private uploaded files live in `storage/app/private/resources/` — never in `public/`
- All file serving goes through controller methods that check: published status, lock status, role, and file existence
- `stream` and `download` routes are completely separate — stream uses `response()->file()` (inline), download uses `Storage::download()` (attachment)

### Authentication & sessions

- Passwords hashed with bcrypt
- Session timeout configurable (`SESSION_LIFETIME`, default 120 min)
- Account lockout after N failed logins — tracked in `users.failed_login_attempts`, stored in `users.locked_until`
- Lockouts logged to `activity_logs` with IP and user agent
- Remember-me tokens rotated on each use

### Input & output protection

- CSRF protection on all web forms (Laravel default)
- All SQL via Eloquent ORM (parameterized queries — no SQL injection)
- All Blade output via `{{ }}` (auto-escaped — no XSS)
- MIME type + file extension validation on upload
- `SecurityHeaders` middleware adds `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy` headers on every response

### Rate limiting

| Limiter | Route | Limit |
|---|---|---|
| `api-login` | `POST /api/auth/login` | 5 requests/min per IP |
| `api-download` | `GET /api/resources/{id}/download` | 10 requests/min per user |

### Audit trail

- Every admin action logged in `audit_logs` with user ID, action type, resource ID, IP address, and timestamp
- Failed login attempts and lockouts logged in `activity_logs` with user agent
- Share link creation and revocation logged

---

## Role Permissions Matrix

| Permission | Admin | Editor | Viewer |
|---|---|---|---|
| Browse / view published documents | ✔ | ✔ | ✔ |
| Download documents | ✔ | ✔ | ✔ |
| Preview documents (PDF / image) | ✔ | ✔ | ✔ |
| Search (keyword + AI semantic) | ✔ | ✔ | ✔ |
| Favorites, bookmarks, reading lists | ✔ | ✔ | ✔ |
| Ratings and reviews | ✔ | ✔ | ✔ |
| Share link generation | ✔ | ✔ | ✔ |
| Upload new documents | ✔ | ✔ | ✗ |
| Edit / delete documents | ✔ | ✔ | ✗ |
| Lock / unlock documents | ✔ | ✔ | ✗ |
| Approve / reject documents | ✔ | ✔ | ✗ |
| Manage categories and tags | ✔ | ✔ | ✗ |
| Document version management | ✔ | ✔ | ✗ |
| View analytics / access logs | ✔ | ✔ | ✗ |
| Trigger search reindex | ✔ | ✔ | ✗ |
| Export audit / activity logs (CSV) | ✔ | ✗ | ✗ |
| Manage users and roles | ✔ | ✗ | ✗ |
| View audit logs | ✔ | ✗ | ✗ |
| Manage queue / failed jobs | ✔ | ✗ | ✗ |
| Manage storage quotas | ✔ | ✗ | ✗ |
| System settings | ✔ | ✗ | ✗ |
| Broadcast notifications | ✔ | ✗ | ✗ |
| Approve account requests | ✔ | ✗ | ✗ |

---

## Tech Stack

| Layer | Technology | Version |
|---|---|---|
| Backend framework | Laravel | 12.x |
| Language | PHP | 8.2+ |
| Database | MySQL | 8.0+ (SQLite for dev) |
| Templating | Laravel Blade | — |
| CSS framework | Tailwind CSS | 3.x |
| JS interactivity | Alpine.js | 3.x |
| UI components | Flowbite | 2.x |
| Asset bundler | Vite | 7.x |
| PDF preview | PDF.js | bundled locally |
| API auth | Laravel Sanctum | 4.x |
| Auth scaffolding | Laravel Breeze | 2.x |
| PDF text extraction | smalot/pdfparser | 2.x |
| DOCX text extraction | phpoffice/phpword | 1.x |
| XLSX text extraction | phpoffice/phpspreadsheet | 3.x |
| PPTX text extraction | phpoffice/phppresentation | 1.x |
| Queue / cache | Database (dev), Redis (prod) | — |
| Full-text search | MySQL FULLTEXT | — |
| Semantic search | TF-IDF cosine similarity (pure PHP) | — |
| File storage | Local disk (`storage/app/private/`) | — |

---

## System Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              Browser / API client                           │
└──────────────┬──────────────────────────────────────────┬───────────────────┘
               │  /admin/*                                │  /*  and  /api/*
               ▼                                          ▼
    ┌──────────────────────┐                  ┌──────────────────────────┐
    │    Admin Panel       │                  │    Client Web App        │
    │  Blade + Tailwind    │                  │  Blade + Tailwind        │
    │  Alpine.js+Flowbite  │                  │  Alpine.js + Flowbite    │
    │  role: admin/editor  │                  │  role: all authenticated │
    └──────────┬───────────┘                  └────────────┬─────────────┘
               │                                           │
               └─────────────────┬─────────────────────────┘
                                  ▼
              ┌──────────────────────────────────────────────────┐
              │                  Laravel 12                      │
              │  ┌────────────┐  ┌──────────────┐  ┌─────────┐ │
              │  │ Middleware  │  │   Router     │  │  Auth   │ │
              │  │ auth,active │  │  web+api     │  │ Breeze+ │ │
              │  │ role:…     │  │  +console    │  │ Sanctum │ │
              │  └────────────┘  └──────────────┘  └─────────┘ │
              │  ┌──────────────────────────────────────────┐   │
              │  │             Controllers (40)             │   │
              │  │  Client (15) │ Admin (14) │ API (3)      │   │
              │  └──────────────────────────────────────────┘   │
              │  ┌──────────────────────────────────────────┐   │
              │  │          Eloquent ORM (24 models)        │   │
              │  └──────────────────────────────────────────┘   │
              │  ┌──────────────┐  ┌─────────────────────────┐  │
              │  │   Services   │  │     Queue Jobs (2)       │  │
              │  │ ContentExtr. │  │ ExtractDocumentContent   │  │
              │  │ TfidfService │  │ IndexDocumentTfidf       │  │
              │  └──────────────┘  └─────────────────────────┘  │
              │  ┌──────────────────────────────────────────┐   │
              │  │          Artisan Commands (8+2)          │   │
              │  │  search:reindex  search:build-tfidf      │   │
              │  │  dms:prune-*     dms:archive-expired     │   │
              │  │  docs:import-laravel   docs:build-*      │   │
              │  └──────────────────────────────────────────┘   │
              │  ┌──────────────────────────────────────────┐   │
              │  │    Scheduler (cron every minute)         │   │
              │  │  prune trash/shares/tokens  │  archive   │   │
              │  └──────────────────────────────────────────┘   │
              └────────────────────────┬─────────────────────────┘
                                       ▼
              ┌──────────────────────────────────────────────────┐
              │                 Data Layer                       │
              │  MySQL 31 tables  │  storage/app/private/        │
              │                   │  ├── resources/{uuid}.ext   │
              │  resource_emb.    │  ├── search/tfidf_idf.json  │
              │  (TF-IDF vectors) │  └── documents/laravel-docs/│
              └──────────────────────────────────────────────────┘
```

---

## Future Enhancements

- OCR scanning for scanned/image-only PDFs (`tesseract_ocr` / `spatie/pdf-to-text`)
- AI document summarization
- Chat with documents (RAG pipeline)
- Elasticsearch integration for large-scale deployments
- Real-time collaboration (document co-editing)
- Push notifications (browser Web Push API)
- Mobile app (via the existing REST API layer)
- Multi-tenant / SaaS mode

---

*Built with Laravel 12 · Tailwind CSS · Alpine.js · Vite · TF-IDF offline semantic search*
