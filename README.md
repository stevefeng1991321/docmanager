# 📚 Document Management System (PHP + Laravel + XAMPP + MySQL)

A **secure, scalable, enterprise-ready document management system** built with **Laravel** running on **XAMPP (Apache + MySQL)**. It supports advanced search (keyword + AI semantic search), version control, secure file handling, preview system, analytics, and extensible AI-powered document intelligence.

The system ships as **two separate web applications** sharing a single Laravel backend and database:

| App | URL Prefix | Audience |
|---|---|---|
| **Admin Panel** | `/admin` | Admins and Editors — full document and user management |
| **Client Web App** | `/` | All authenticated users — browse, search, preview, download |

All files are stored locally on the server machine (no cloud dependency).

---

# ⚙️ Prerequisites

| Requirement | Version |
|---|---|
| PHP | 8.2+ |
| MySQL | 8.0+ |
| Composer | 2.x |
| Node.js / npm | 18+ |
| PHP Extensions | `pdo_mysql`, `fileinfo`, `mbstring`, `openssl`, `zip` |
| LibreOffice | Optional — required only for DOCX → PDF full-fidelity preview |

### Enable required PHP extensions

Open your `php.ini` (run `php --ini` to find the path) and uncomment these lines:

```ini
extension=fileinfo
extension=pdo_mysql
```

Then verify they loaded:

```bash
php -r "echo extension_loaded('fileinfo') && extension_loaded('pdo_mysql') ? 'OK' : 'Missing';"
```

---

# 🚀 Local Development Setup

### Step 1 — Clone the repository

```bash
git clone <repo-url>
cd docmanager
```

### Step 2 — Install PHP dependencies

```bash
composer install
```

### Step 3 — Install JS dependencies

```bash
npm install
```

### Step 4 — Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your MySQL credentials:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=docmanager
DB_USERNAME=root
DB_PASSWORD=
```

### Step 5 — Run database migrations

```bash
php artisan migrate
```

### Step 6 — Fix Vite IPv6/IPv4 mismatch (Windows)

On Windows, Vite defaults to IPv6 (`[::1]`) which breaks CSS loading. Add `host` to `vite.config.js`:

```js
export default defineConfig({
    server: {
        host: '127.0.0.1',
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

### Step 7 — Start both dev servers (two terminals)

```bash
# Terminal 1 — Laravel backend
php artisan serve --host=127.0.0.1 --port=8000

# Terminal 2 — Vite (CSS + JS + HMR)
npm run dev
```

The app will be available at **http://127.0.0.1:8000**

### Step 8 — (Optional) Start the queue worker

Required for file content extraction, embedding generation, and bulk downloads:

```bash
php artisan queue:work --queue=default
```

---

# 🎨 Frontend Stack

Both the **Admin Panel** and **Client Web App** are built with **Tailwind CSS + Alpine.js + Flowbite**, compiled and bundled locally — no CDN required, fully offline-capable.

| Library | Role |
|---|---|
| **Tailwind CSS** | Utility-first CSS — compiled to a single static file via `npm run build` |
| **Alpine.js** | Lightweight JS for interactivity (modals, dropdowns, upload progress, search filters) |
| **Flowbite** | Pre-built Tailwind components (tables, sidebars, stat cards, dashboards, modals) |

### Shared layout approach

* `resources/views/layouts/admin.blade.php` — Admin Panel shell (sidebar nav, topbar)
* `resources/views/layouts/app.blade.php` — Client Web App shell (header, category sidebar)
* All Blade views extend one of these two layouts
* XSS protection via Blade's `{{ }}` auto-escaping on all output

### Install frontend dependencies

```bash
npm install tailwindcss @tailwindcss/forms flowbite alpinejs
npm run build
```

All output is written to `public/build/` — served entirely from the local machine.

---

# ⚙️ Environment Variables

Key `.env` settings:

| Variable | Description |
|---|---|
| `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | MySQL connection |
| `QUEUE_CONNECTION` | `database` or `redis` |
| `CACHE_DRIVER` | `redis` or `file` |
| `OPENAI_API_KEY` | For OpenAI embedding model (optional) |
| `VECTOR_DB_URL` | Qdrant / Chroma / Weaviate endpoint |
| `SESSION_LIFETIME` | Session timeout in minutes (default: `120`) |
| `MAX_UPLOAD_SIZE_MB` | Maximum file upload size in MB (default: `50`) |
| `SHARE_LINK_EXPIRY_HOURS` | Default signed share link expiry (default: `24`) |
| `LOCK_TIMEOUT_MINUTES` | Document lock auto-release timeout (default: `30`) |
| `TRASH_RETENTION_DAYS` | Days before soft-deleted documents are auto-purged (default: `30`) |
| `ACCOUNT_LOCKOUT_ATTEMPTS` | Failed login attempts before lockout (default: `5`) |
| `ACCOUNT_LOCKOUT_MINUTES` | Lockout duration in minutes (default: `15`) |

---

# 🏗️ Application Structure

## 🛠️ Admin Panel (`/admin`)

A full back-office interface for Admins and Editors.

### Pages & Modules

| Module | Path | Description |
|---|---|---|
| Dashboard | `/admin` | Stat cards + charts: uploads, downloads, storage, active users, search trends |
| Documents | `/admin/documents` | List, upload, edit, delete, bulk ops, versioning, document locking |
| Trash | `/admin/documents/trash` | Restore or permanently delete soft-deleted documents |
| Categories | `/admin/categories` | Manage hierarchical category tree |
| Tags | `/admin/tags` | Create and manage tags |
| Users | `/admin/users` | Create staff accounts; view, activate/deactivate, assign roles |
| Pending Accounts | `/admin/users/pending` | Review, activate, or reject (with reason) self-registered accounts; badge count shown in sidebar nav |
| Roles | `/admin/roles` | View and configure role permissions |
| Audit Logs | `/admin/audit-logs` | Full action log with IP, user, resource, timestamp — exportable to CSV |
| Activity Logs | `/admin/activity-logs` | Session events — login, logout, failed access, account lockouts |
| Jobs | `/admin/jobs` | Monitor queue — pending, processing, and failed jobs |
| Storage | `/admin/storage` | Storage usage per user, global quota management |
| Notifications | `/admin/notifications` | Manage and broadcast system notifications |
| Search Index | `/admin/search` | Re-index search engine, view search analytics |
| Settings | `/admin/settings` | Upload limits, session timeout, share link expiry |

---

## 🌐 Client Web App (`/`)

A clean document portal for all authenticated users.

### Pages & Modules

| Module | Path | Description |
|---|---|---|
| Register | `/register` | Self-registration form — username + password only; account created as Pending |
| Login | `/login` | Login form; Pending accounts are blocked with a "awaiting activation" message |
| Home / Browse | `/` | Document grid/list with category sidebar, sort controls, pagination |
| Search | `/search` | Hybrid search with filters, sort, highlighted results, search history |
| Document Detail | `/documents/{id}` | Metadata, breadcrumb, preview, download, version history (read-only), related documents |
| Preview | `/documents/{id}/preview` | PDF.js / image / text preview |
| Categories | `/categories/{slug}` | Browse documents by category with breadcrumb navigation |
| Tags | `/tags/{slug}` | Browse documents by tag |
| Favorites | `/favorites` | Bookmarked documents |
| Recently Viewed | `/history` | Documents the user has opened, most recent first |
| Download History | `/downloads` | User's own file download log |
| Saved Searches | `/saved-searches` | Manage saved search queries |
| Reading Lists | `/reading-lists` | Create and manage named document collections (e.g. "Linux study pack") |
| Notifications | `/notifications` | Inbox — mark as read, dismiss, manage notification preferences |
| Profile | `/profile` | Edit name, password, avatar, notification preferences |

---

# 🚀 Core Features

## 👤 Authentication & Authorization

* Clients self-register via the Client Web App using **username + password only** (no email or phone required)
* New accounts start as **Pending** — must be activated by Admin before the user can log in
* Pending users see an "awaiting activation" message on login; enforced via auth middleware
* Admin and Editor accounts are created directly by Admin (no self-registration for staff roles)
* Password reset done by Admin directly — no email-based reset flow
* Remember me / persistent session (configurable)
* Account lockout after N consecutive failed login attempts — configurable via `ACCOUNT_LOCKOUT_ATTEMPTS` / `ACCOUNT_LOCKOUT_MINUTES`
* Session-based authentication (Laravel Breeze)
* Role-based + Permission-based access control (RBAC)

  * Admin
  * Editor
  * Viewer (default role for self-registered clients)

### Username Rules (registration)

* Length: 3–30 characters
* Allowed characters: letters, numbers, underscores, hyphens
* Must be unique (validated on submit)
* Cannot be changed after registration (contact Admin to update)

### Role Permissions

| Permission | Admin | Editor | Viewer |
|---|---|---|---|
| Upload documents | ✔ | ✔ | ✗ |
| Edit / delete documents | ✔ | ✔ | ✗ |
| Download documents | ✔ | ✔ | ✔ |
| View / preview documents | ✔ | ✔ | ✔ |
| Search | ✔ | ✔ | ✔ |
| Favorites & bookmarks | ✔ | ✔ | ✔ |
| Manage tags & categories | ✔ | ✔ | ✗ |
| Manage document versions | ✔ | ✔ | ✗ |
| Lock / unlock documents | ✔ | ✔ | ✗ |
| View analytics | ✔ | ✔ | ✗ |
| Export logs (CSV) | ✔ | ✗ | ✗ |
| Manage users & roles | ✔ | ✗ | ✗ |
| View audit logs | ✔ | ✗ | ✗ |
| Manage jobs / queue | ✔ | ✗ | ✗ |
| Manage storage quotas | ✔ | ✗ | ✗ |
| Re-index search | ✔ | ✗ | ✗ |
| System settings | ✔ | ✗ | ✗ |

---

# 🛠️ Admin Panel Features

### Document Management
* Upload documents with real-time progress indicator (Alpine.js)
* Chunked upload support for files over 50 MB
* Edit metadata and update / delete documents
* Soft delete documents (moved to Trash, restorable)
* Bulk upload / bulk delete / bulk download (ZIP via `ZipArchive`)
* Bulk tag and category assignment
* Document locking — prevent concurrent edits (lock owned by editor, auto-release after timeout)
* Document approval workflow — Draft → Pending Review → Published states
* Document expiry / archival — auto-archive documents after a configurable period

### Version & Integrity
* Upload new version, restore any previous version, view full version history
* SHA256 hash per version for integrity verification
* Per-document access log — who viewed or downloaded each document and when

### User & Role Management
* Create staff accounts directly (Admin / Editor roles)
* Review self-registered client accounts — activate (Pending → Active) or reject with a written reason
* Bulk activate or bulk reject pending accounts
* Admin notified via dashboard badge when new registrations are pending
* Account activation and rejection logged in `audit_logs`
* Edit username (Admin can change any username on behalf of a user)
* Activate / deactivate accounts
* Reset user passwords directly (no email required)
* Assign and change roles (Admin / Editor / Viewer)
* Role management page — configure which permissions each role carries
* Storage quota per user (configurable, enforced on upload)
* Process client account deletion requests

### Analytics & Logs
* Dashboard stat cards: total documents, storage used, downloads today, active users
* Charts: upload trends (7d / 30d), top downloaded files, popular search terms
* Audit logs with IP, user, action, resource, timestamp — exportable to CSV
* Activity logs: login, logout, failed access, lockouts — exportable to CSV
* Queue monitor: pending, processing, and failed background jobs

### Other Admin Features
* Re-index search engine (metadata, fulltext, embeddings)
* Manage categories (hierarchical tree CRUD)
* Manage tags (create, rename, merge, delete)
* Broadcast system notifications
* System settings: upload size limit, session timeout, share link expiry

---

# 🌐 Client Web App Features

### Browse & Discover
* Document grid / list view (toggle) with category sidebar
* Sort by: name, upload date, file size, file type, most downloaded
* Pagination on all document lists and search results
* Breadcrumb navigation for category drill-down
* Related documents panel on Document Detail page

### Search
* Hybrid search: keyword + full-text + AI semantic + filters
* Search filters: file type, date range, uploader, tags, category, file size
* Highlighted keyword matches in title, description, and preview text
* Recent search history (last 10 queries, clearable)
* Saved searches — name and save a search query for quick re-use
* Sort search results by: relevance (default), date, name, size

### Document Actions
* Preview files in-browser: PDF (PDF.js), images, DOCX text
* Secure download (controller-gated, permission-checked)
* View version history (read-only)
* Generate and copy signed share link
* Rate document (1–5 stars) — average rating shown on document detail and search results
* Save in-document page bookmark — remember a specific page number inside a PDF for later resumption

### Personal Features
* Favorites / Bookmarks — save documents for quick access
* Recently Viewed — auto-tracked list of opened documents
* Download History — user's own download log
* Reading Lists — create named collections of documents (e.g. "Linux study pack", "Project X references"); add/remove documents, reorder, make private or share with other users
* In-document Page Bookmarks — save a specific page number within a PDF; resume reading from where you left off; multiple bookmarks per document with optional label
* Document Ratings — rate any document 1–5 stars; average rating visible on document cards and detail pages; used as a search ranking signal
* Notification inbox — mark as read, dismiss, manage notification preferences per event type
* Profile page — edit display name, avatar, change password, set notification preferences
* Username change request — submit a request to Admin (username cannot be changed directly by the user)
* Account deletion request — submit a deletion request; Admin reviews and processes it

---

# 📁 File Storage (Local XAMPP)

All files are stored locally:

```
storage/app/resources/
```

* No cloud dependency
* Secure controller-based access
* No direct public file exposure

### Supported File Types & Limits

| Category | Formats | Max Size |
|---|---|---|
| Documents | PDF, DOCX, TXT, XLSX, PPTX | 50 MB (default, configurable) |
| Images | JPG, PNG, GIF, WEBP | 20 MB |
| Archives | ZIP (metadata only, no content extraction) | 100 MB |

---

# 🔐 Security Architecture

## File Access Protection

* No direct `/storage` access
* Download via controller with role/permission check
* Signed / validated download requests

## Password & Session Security

* bcrypt / argon2 password hashing
* Password reset done by Admin directly — no email-based reset flow required
* Session timeout configurable via `SESSION_LIFETIME` (default: 120 minutes)
* Account lockout after N failed logins — configurable via `ACCOUNT_LOCKOUT_ATTEMPTS` / `ACCOUNT_LOCKOUT_MINUTES`, logged to `activity_logs`
* Remember me tokens rotated on each use

## System Security

* CSRF protection (Laravel default)
* Input validation on all form fields
* SQL injection protection (Eloquent ORM)
* XSS protection via Blade's `{{ }}` auto-escaping on all output
* File type + size validation (MIME check + extension whitelist)
* Rate limiting: 60 requests/min on API endpoints, 10 downloads/min per user
* HTTPS / SSL recommended for all production deployments

## Audit & IP Logging

* All admin actions logged with user ID, action, resource, and **IP address**
* Failed access attempts and lockouts logged to `activity_logs` with IP and user agent

---

## File Integrity

* SHA256 file hashing on upload
* SHA256 hash stored per version in `document_versions`
* Duplicate detection (same hash → reject or link to existing)
* Optional virus scan hook

---

# 🗄️ Database Schema

The full MySQL schema (all `CREATE TABLE` statements with indexes, foreign keys, and constraints) is in:

📄 **[document_management_schema.sql](document_management_schema.sql)**

### Tables

| Table | Purpose |
|---|---|
| `users` | Accounts, roles, `status` ENUM (`pending`/`active`/`inactive`), session tokens |
| `personal_access_tokens` | Sanctum API tokens |
| `resources` | Document metadata, file paths, extracted content |
| `document_versions` | Version history per document |
| `document_access_logs` | Per-document record of who viewed or downloaded each version and when |
| `categories` | Hierarchical category tree |
| `tags` | Tag definitions |
| `resource_tags` | Many-to-many pivot: resources ↔ tags |
| `favorites` | Per-user bookmarked documents |
| `recently_viewed` | Auto-tracked per-user document view history (last 50 per user) |
| `saved_searches` | Named saved search queries per user |
| `shares` | Signed share links with expiry and revocation |
| `audit_logs` | Admin/system-level actions with IP |
| `activity_logs` | User session events with IP and user agent |
| `search_logs` | Every search query with type and result count |
| `download_logs` | Per-file download records |
| `resource_embeddings` | AI vector chunks per document for semantic search |
| `notifications` | Per-user notifications with read/unread state |
| `user_preferences` | Per-user settings (notification opt-ins, view mode, etc.) |
| `reading_lists` | User-created named document collections |
| `reading_list_items` | Many-to-many pivot: reading lists ↔ documents, with sort order |
| `bookmarks` | Per-user in-document page bookmarks (page number + optional label per PDF) |
| `document_ratings` | Per-user 1–5 star rating per document; average rating computed for display and search ranking |

---

# 📂 Folder & Category System

Documents can be organized into a **hierarchical category tree**:

```
Root
├── Finance
│   ├── Invoices
│   └── Reports
├── Legal
│   └── Contracts
└── HR
    └── Policies
```

* Admins and Editors manage the category tree
* Documents belong to one category
* Breadcrumb navigation reflects the category path
* Category-based filtering available in search

---

# 🔎 Advanced Search System

The system uses a **multi-layer hybrid search engine**.

## 1. Metadata Search (Fast)

Search by: file name, title, description.

## 2. Full-Text Search (MySQL)

Full-text index on `resources(title, description, content)` — defined in schema.

## 3. File Content Search

Text extracted from PDF, DOCX, and TXT files and stored in `resources.content (LONGTEXT)`.

## 4. AI Semantic Search (Vector-Based)

### Architecture:

```
User Query
   ↓
Embedding Model (OpenAI / Local LLM)
   ↓
Vector Database (Qdrant / Chroma / Weaviate)
   ↓
Similarity Matching
   ↓
Ranked Results
```

### Features:

* Meaning-based search (not keyword-based)
* Finds similar documents
* Natural language queries

## 5. Hybrid Search Engine

```
Keyword Search (MySQL)
+ Full-text Search
+ Vector Search (AI)
+ Content Search
→ Ranking Engine
→ Final Results
```

---

## 🔍 Search Ranking System

Scoring model:

* Title match → +5
* Filename match → +4
* Content match → +2
* Vector similarity → 0–1 score
* Recent files boost → +1
* Average rating boost → 0–1 (normalised from 1–5 star average)

---

## 🔎 Search Filters

Filters are combinable with any search type:

| Filter | Values |
|---|---|
| File type | `pdf`, `docx`, `xlsx`, `jpg`, `png`, `txt` |
| Upload date | date range (from / to) |
| Uploader | user ID or name |
| Tags | one or more tag slugs |
| Category | category ID or slug |
| File size | min / max in bytes |

---

## 🔃 Sort Options

Available on document lists and search results:

| Sort | Direction |
|---|---|
| Relevance score | desc (search only) |
| Upload date | asc / desc |
| File name | asc / desc |
| File size | asc / desc |
| Download count | desc |

---

## 🔎 Search Highlighting & History

* Highlights matched keywords in: title, description, preview text
* Last 10 search queries shown as recent history (clearable per user)
* Saved searches — name, save, and re-run any query with one click
* Basic autocomplete on metadata fields (title, filename) without Elasticsearch

---

# 📄 File Content Extraction

Supported formats:

* PDF → `smalot/pdfparser`
* DOCX → `phpoffice/phpword` (also used for DOCX → text preview)
* TXT → native PHP

### Install libraries:

```bash
composer require smalot/pdfparser
composer require phpoffice/phpword
```

---

# 👁️ File Preview System

* PDF preview via **PDF.js** (browser-based, bundled locally — no CDN)
* Image preview (JPG, PNG, GIF, WEBP)
* DOCX text preview (extracted via phpoffice/phpword)
* DOCX → PDF conversion for full-fidelity preview (optional, requires LibreOffice on server)

```
<iframe src="/documents/{id}/preview"></iframe>
```

---

# 📤 File Upload

* Real-time upload progress indicator (Alpine.js + `XMLHttpRequest`)
* Chunked upload for files over 50 MB (avoids PHP `upload_max_filesize` limits)
* File type validation: MIME type check + extension whitelist
* File size validation: per-category limits enforced server-side
* SHA256 hash computed on server after upload

---

# 🔄 Document Version Control

## Features:

* Maintain multiple file versions per document
* Restore previous versions
* Track version history with uploader and timestamp
* SHA256 hash per version for integrity verification
* Per-document access log — who viewed or downloaded each version and when

## Flow:

```
Upload new version
   ↓
Store file + compute SHA256
   ↓
Create version entry (document_versions)
   ↓
Keep all previous versions accessible
```

---

# 🔒 Document Locking

Prevents concurrent edits to the same document:

* Locking owned by the editing user, shown to others as "locked by [user]"
* Auto-released after configurable timeout (default: 30 minutes)
* Admins can force-unlock any document
* Lock state stored in `resources` table (`locked_by`, `locked_at`)

---

# 📋 Document Approval Workflow

Optional publishing states for controlled document release:

```
Draft → Pending Review → Published
              ↓
           Rejected (back to Draft)
```

* Editors submit documents for review
* Admins approve or reject with a comment
* Only Published documents are visible to Viewers
* State transitions logged in `audit_logs`

---

# 📤 Bulk Operations

Available to Admin and Editor roles:

* Bulk upload (multiple files in one request, with per-file progress)
* Bulk soft delete (move to Trash)
* Bulk download (served as ZIP archive via PHP `ZipArchive`)
* Bulk tag / category assignment

---

# 🗑️ Soft Delete & Trash

* Deleted documents move to **Trash** (`deleted_at` timestamp — Laravel soft deletes)
* Admins can restore or permanently delete from Trash
* Configurable auto-purge retention period (default: 30 days)
* Trash accessible only to Admins at `/admin/documents/trash`

---

# 🔗 Document Sharing

* Generate a **signed, time-limited share link** for any document
* Share link records stored in `shares` table (token, expiry, resource, created by)
* Accessible by unauthenticated users (read-only preview + download)
* Configurable expiry: 1 hour / 24 hours / 7 days
* Links can be revoked before expiry
* Share link generation and revocation logged in `audit_logs`

---

# ⭐ Favorites & Personal History

* **Favorites** — bookmark any document; accessible at `/favorites`; stored in `favorites` table
* **Recently Viewed** — auto-tracked on document open; accessible at `/history`; stores last 50 entries per user in `recently_viewed` table
* **Download History** — user's own download log at `/downloads`; sourced from `download_logs`
* **Saved Searches** — save a named search query for quick re-use at `/saved-searches`; stored in `saved_searches` table
* Display preferences (grid/list view, items per page) stored in `user_preferences`

---

# ⚙️ Background Processing (Queues)

Used for:

* File content extraction
* Embedding generation
* Search indexing
* Bulk ZIP download generation

```
Upload → Queue Job → Process → Store index
```

Laravel Queues:

* `database` (default, no extra setup)
* `redis` (recommended for production)

Start the worker:

```bash
php artisan queue:work --queue=default
```

Monitor failed jobs at `/admin/jobs`.

---

# 🧠 AI Semantic Search (RAG-Ready)

### Pipeline:

* Extract text
* Chunk documents
* Generate embeddings (OpenAI or local model)
* Store in vector DB + `resource_embeddings` table
* Query using similarity search

### Enables:

* Chat with documents
* Semantic retrieval
* Smart search results

---

# 📊 Admin Analytics Dashboard

### Stat Cards
* Total documents / storage used
* Downloads today / this week
* Active users (last 30 days)
* Failed jobs count

### Charts
* Upload trends (7-day and 30-day bar chart)
* Top 10 most downloaded files
* Popular search terms (word cloud / table)
* Storage usage by file type (pie chart)

---

# 🏷️ Tags System

* Admins and Editors create and assign tags (e.g. `finance`, `legal`, `invoice`, `report`)
* Multiple tags per document via `resource_tags` pivot
* Tag merge — combine duplicate tags without losing document associations
* Tag-based filtering in search

---

# 🔔 Notification System

| Event | In-App |
|---|---|
| File uploaded | ✔ |
| Version updated | ✔ |
| Access denied | ✔ (admin) |
| Account locked | ✔ |
| New pending registration | ✔ (admin) |
| Account activated / rejected | ✔ (user) |
| Document approved / rejected | ✔ (editor) |
| Username / deletion request received | ✔ (admin) |

**Delivery:**

* In-app notifications stored in `notifications` table with `is_read` flag

**User preferences:**

* Per-user opt-in/out for each notification event type
* Managed at `/profile` and stored in `user_preferences`

---

# 📱 API Layer

Authentication: **Laravel Sanctum** (Bearer token)

| Endpoint | Method | Description |
|---|---|---|
| `/api/auth/login` | POST | Obtain API token |
| `/api/auth/logout` | POST | Revoke API token |
| `/api/resources` | GET | List documents (paginated) |
| `/api/resources/{id}` | GET | Get document metadata |
| `/api/resources` | POST | Upload a document |
| `/api/resources/{id}` | PUT | Update document metadata |
| `/api/resources/{id}` | DELETE | Soft delete a document |
| `/api/resources/{id}/download` | GET | Download a file |
| `/api/resources/{id}/share` | POST | Generate a signed share link |
| `/api/search` | GET | Search documents (all types + filters + sort) |

---

# 🧊 Caching Layer

* **Driver**: Redis (recommended) or file cache
* Search results cached by query hash (configurable TTL)
* Embeddings cached to avoid re-generation
* Analytics aggregates cached to reduce DB load
* Cache invalidated on document upload, edit, or delete

---

# 🧬 OCR (Optional Upgrade)

For scanned PDFs:

* Extract text from images inside PDFs
* Enables full search support for scanned documents
* Library: `thiagoalessio/tesseract_ocr` or `spatie/pdf-to-text`

---

# ⚡ Elasticsearch Integration (Optional)

For large-scale deployments:

* Fast indexing
* Advanced ranking
* Auto-suggestions (replaces basic autocomplete)
* Replaces MySQL FULLTEXT in production

---

# 🧱 Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel (PHP 10+ / 11+) |
| Server | Apache (XAMPP) |
| Database | MySQL 8 |
| Templating | Laravel Blade (XSS-safe via `{{ }}`) |
| CSS Framework | Tailwind CSS (compiled, offline-ready) |
| JS Interactivity | Alpine.js (modals, dropdowns, upload progress) |
| UI Components | Flowbite (Tailwind component library, bundled locally) |
| PDF Preview | PDF.js (bundled locally) |
| Queue / Cache | Redis |
| Full-text Search | MySQL FULLTEXT |
| AI Vector Search | Qdrant / Chroma / Weaviate |
| Ranking Engine | Custom hybrid scorer |
| Storage | Local Disk (`storage/app/resources/`) |
| PHP Libraries | smalot/pdfparser, phpoffice/phpword, ZipArchive |
| Auth | Laravel Breeze + Sanctum (API) |

---

# ⚙️ System Architecture

```
┌─────────────────────────┐   ┌─────────────────────────────┐
│   Admin Panel (/admin)  │   │   Client Web App (/)        │
│   Blade + Tailwind CSS  │   │   Blade + Tailwind CSS      │
│   Alpine.js + Flowbite  │   │   Alpine.js + Flowbite      │
│   Admin/Editor only     │   │   All authenticated users   │
└────────────┬────────────┘   └──────────────┬──────────────┘
             │                               │
             └──────────────┬────────────────┘
                            ↓
                    Laravel Backend
                            ↓
          ─────────────────────────────────────────
          │ Auth (Breeze)     │ RBAC │ API (Sanctum)│
          │ File Manager      │ Search Engine        │
          │ AI Module         │ Queue (Redis)         │
          │ Cache (Redis)     │ Notifications         │
          │ Approval Workflow │ Document Locking      │
          ─────────────────────────────────────────
                            ↓
          MySQL + Local Storage + Vector DB (Qdrant/Chroma)
```

---

# 🔮 Future Enhancements

* AI document summarization
* Chat with documents (RAG system)
* OCR scanning for all PDFs
* Real-time collaboration
* Elasticsearch production scaling
* Multi-tenant SaaS version
* Push notifications (browser)
* Mobile app (via API layer)

---

# 🎯 Summary

This system is a **complete enterprise-grade document management platform** featuring:

✔ Dual-app architecture — Admin Panel (`/admin`) + Client Web App (`/`)
✔ Offline-capable UI (Tailwind CSS + Alpine.js + Flowbite, no CDN)
✔ Local file storage (XAMPP)
✔ Secure role-based authentication (RBAC) with account lockout
✔ Client self-registration (username + password) with mandatory Admin activation before first login
✔ Hybrid search engine (keyword + full-text + AI vector search)
✔ Combinable search filters + sort options
✔ Search history, saved searches, and basic autocomplete
✔ File preview system (PDF.js, bundled locally)
✔ Document version control with SHA256 integrity per version + per-document access log
✔ Document locking and approval workflow (Draft → Review → Published)
✔ Folder / category hierarchy with breadcrumbs
✔ Bulk operations (upload, delete, download ZIP, tag assignment)
✔ Soft delete with restorable Trash and auto-purge
✔ Document sharing with signed time-limited links + revocation
✔ Favorites, recently viewed, download history, saved searches
✔ Reading lists — user-created named document collections
✔ In-document page bookmarks — resume reading from a saved page in any PDF
✔ Document ratings (1–5 stars) — community quality signal, used in search ranking
✔ Background job processing (queues) with queue monitor
✔ Advanced analytics dashboard (stat cards + charts)
✔ In-app notification system with per-user preferences
✔ REST API layer (Laravel Sanctum)
✔ Redis caching layer
✔ Storage quota management per user

---

🚀 This project is now architected as a **full SaaS-level document intelligence platform**, not just a file manager.
