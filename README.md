# 📚 Document Management System (PHP + Laravel + XAMPP + MySQL)

A **secure, scalable, enterprise-ready document management system** built with **Laravel** running on **XAMPP (Apache + MySQL)**. It supports advanced search (keyword + AI semantic search), version control, secure file handling, preview system, analytics, and extensible AI-powered document intelligence.

All files are stored locally on the server machine (no cloud dependency).

---

# ⚙️ Prerequisites

| Requirement | Version |
|---|---|
| PHP | 10+ / 11+ |
| MySQL | 8.0+ |
| XAMPP | 8.x (Apache + MySQL) |
| Composer | 2.x |
| Node.js / npm | 18+ (for Tailwind CSS build) |
| Redis | 7+ (for queues and caching) |

---

# 🚀 Installation

```bash
# 1. Clone the repository
git clone <repo-url>
cd docmanager

# 2. Install PHP dependencies
composer install

# 3. Install frontend dependencies
npm install && npm run build

# 4. Configure environment
cp .env.example .env
php artisan key:generate

# 5. Edit .env — set DB credentials, queue driver, cache driver, mail settings

# 6. Run database migrations
php artisan migrate --seed

# 7. Start the queue worker
php artisan queue:work

# 8. Serve via XAMPP or built-in server
php artisan serve
```

---

# 🎨 Frontend Stack

The UI is built with **Tailwind CSS + Alpine.js + Flowbite**, all compiled and bundled locally — no CDN required, fully offline-capable.

| Library | Role |
|---|---|
| **Tailwind CSS** | Utility-first CSS — compiled to a single static file via `npm run build` |
| **Alpine.js** | Lightweight JS for interactivity (modals, dropdowns, file upload progress, search filters) |
| **Flowbite** | Pre-built Tailwind components (file tables, sidebars, stat cards, dashboards) |

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
| `MAIL_MAILER`, `MAIL_HOST`, etc. | Email notification settings |
| `OPENAI_API_KEY` | For OpenAI embedding model (optional) |
| `VECTOR_DB_URL` | Qdrant / Chroma / Weaviate endpoint |

---

# 🚀 Core Features

## 👤 Authentication & Authorization

* User login & registration
* Password reset via signed email link
* Session-based authentication (Laravel Breeze)
* Role-based + Permission-based access control (RBAC)

  * Admin
  * Editor
  * Viewer

### Role Permissions

| Permission | Admin | Editor | Viewer |
|---|---|---|---|
| Upload documents | ✔ | ✔ | ✗ |
| Edit / delete documents | ✔ | ✔ | ✗ |
| Download documents | ✔ | ✔ | ✔ |
| View / preview documents | ✔ | ✔ | ✔ |
| Search | ✔ | ✔ | ✔ |
| Manage tags & categories | ✔ | ✔ | ✗ |
| Manage document versions | ✔ | ✔ | ✗ |
| View analytics | ✔ | ✔ | ✗ |
| Manage users | ✔ | ✗ | ✗ |
| View audit logs | ✔ | ✗ | ✗ |
| Re-index search | ✔ | ✗ | ✗ |

---

# 🛠️ Admin Features

* Upload documents (PDF, DOCX, XLSX, images, TXT, etc.)
* Update / delete resources
* Soft delete documents (moved to Trash, restorable)
* Bulk upload / bulk delete / bulk download
* Manage users, roles, and account status (activate / deactivate)
* View audit logs & activity tracking (includes IP address)
* Re-index search engine
* Manage document versions
* Manage document categories and tags

---

# 👁️ User Features

* View and preview documents
* Download securely
* Search documents (keyword + semantic + filters)
  * Filter by: file type, upload date range, uploader, tags, category
* Highlight matched keywords in results
* Manage own notifications (mark as read / dismiss)

---

# 📁 File Storage (Local XAMPP)

All files are stored locally:

```
storage/app/resources/
```

* No cloud dependency
* Secure controller-based access
* No direct public file exposure

### Supported File Types

| Category | Formats |
|---|---|
| Documents | PDF, DOCX, TXT, XLSX, PPTX |
| Images | JPG, PNG, GIF, WEBP |
| Archives | ZIP (metadata only, no content extraction) |

---

# 🔐 Security Architecture

## File Access Protection

* No direct `/storage` access
* Download via controller with permission check
* Signed / validated download requests

## Password Security

* bcrypt / argon2 hashing
* Password reset via signed email link (expires after 60 minutes)

## System Security

* CSRF protection (Laravel default)
* Input validation
* SQL injection protection (Eloquent ORM)
* File type + size validation (configurable max size per role)
* Rate limiting on API and download endpoints

## Audit & IP Logging

* All admin actions logged with user ID, action, resource, and **IP address**
* Failed access attempts logged to `activity_logs` with IP and user agent

---

## File Integrity

* SHA256 file hashing on upload
* SHA256 hash stored per version in `document_versions`
* Duplicate detection (same hash → reject or link)
* Optional virus scan hook

---

# 🗄️ Database Schema

The full MySQL schema (all `CREATE TABLE` statements with indexes, foreign keys, and constraints) is in:

📄 **[document_management_schema.sql](document_management_schema.sql)**

### Tables

| Table | Purpose |
|---|---|
| `users` | Accounts, roles, and session tokens |
| `resources` | Document metadata, file paths, extracted content |
| `document_versions` | Version history per document |
| `categories` | Folder/category hierarchy for document organization |
| `tags` | Tag definitions |
| `resource_tags` | Many-to-many pivot: resources ↔ tags |
| `audit_logs` | Admin/system-level actions (upload, delete, permission changes) with IP |
| `activity_logs` | User session events (login, logout, failed access) with IP and user agent |
| `search_logs` | Every search query with type and result count |
| `download_logs` | Per-file download records |
| `resource_embeddings` | AI vector chunks per document for semantic search |
| `notifications` | Per-user notifications with read/unread state |

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

## 🔎 Search Highlighting

Highlights matched keywords in:

* title
* description
* preview text

---

# 📄 File Content Extraction

Supported formats:

* PDF → `smalot/pdfparser`
* DOCX → `phpoffice/phpword`
* TXT → native PHP

### Install libraries:

```bash
composer require smalot/pdfparser
composer require phpoffice/phpword
```

---

# 👁️ File Preview System

* PDF preview via **PDF.js** (browser-based, no plugin required)
* Image preview (JPG, PNG, GIF, WEBP)
* DOCX converted text preview

```
<iframe src="/file-preview/{id}"></iframe>
```

---

# 🔄 Document Version Control

## Features:

* Maintain multiple file versions per document
* Restore previous versions
* Track version history with uploader and timestamp
* SHA256 hash per version for integrity verification

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

# 📤 Bulk Operations

Available to Admin and Editor roles:

* Bulk upload (multiple files in one request)
* Bulk soft delete (move to Trash)
* Bulk download (served as ZIP archive)
* Bulk tag assignment

---

# 🗑️ Soft Delete & Trash

* Deleted documents move to **Trash** (`deleted_at` timestamp — Laravel soft deletes)
* Admins can restore or permanently delete from Trash
* Configurable auto-purge retention period (default: 30 days)

---

# 🔗 Document Sharing

* Generate a **signed, time-limited share link** for any document
* Accessible by unauthenticated users (read-only)
* Configurable expiry: 1 hour / 24 hours / 7 days
* Share link generation logged in `audit_logs`

---

# ⚙️ Background Processing (Queues)

Used for:

* File content extraction
* Embedding generation
* Search indexing
* Email notifications

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

Tracks:

* Most downloaded files
* Popular search terms
* Active users
* Storage usage
* Upload trends

---

# 🏷️ Tags System

* Admins and Editors create and assign tags (e.g. `finance`, `legal`, `invoice`, `report`)
* Multiple tags per document via `resource_tags` pivot
* Tag-based filtering in search

---

# 🔔 Notification System

| Event | Notification |
|---|---|
| File uploaded | Notify relevant users / admins |
| Version updated | Notify document owner |
| Access denied | Alert admin |

**Delivery:**

* In-app notifications (stored in `notifications` table with `is_read` flag)
* Email notifications (via Laravel Mail / SMTP)

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
| `/api/search` | GET | Search documents (all types + filters) |

---

# 🧊 Caching Layer

* **Driver**: Redis (recommended) or file cache
* Search results cached by query hash (configurable TTL)
* Embeddings cached to avoid re-generation
* Analytics aggregates cached to reduce DB load

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
* Auto-suggestions
* Replaces MySQL FULLTEXT in production

---

# 🧱 Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel (PHP 10+ / 11+) |
| Server | Apache (XAMPP) |
| Database | MySQL 8 |
| Templating | Laravel Blade |
| CSS Framework | Tailwind CSS (compiled, offline-ready) |
| JS Interactivity | Alpine.js (modals, dropdowns, upload progress) |
| UI Components | Flowbite (Tailwind component library, bundled locally) |
| PDF Preview | PDF.js |
| Queue / Cache | Redis |
| Full-text Search | MySQL FULLTEXT |
| AI Vector Search | Qdrant / Chroma / Weaviate |
| Ranking Engine | Custom hybrid scorer |
| Storage | Local Disk (`storage/app/resources/`) |
| PHP Libraries | smalot/pdfparser, phpoffice/phpword |
| Auth | Laravel Breeze + Sanctum (API) |

---

# ⚙️ System Architecture

```
Blade Templates
+ Tailwind CSS (compiled, offline)
+ Alpine.js (interactivity)
+ Flowbite (UI components)
      ↓
Laravel Backend
      ↓
-----------------------------------------
| Auth | RBAC | API (Sanctum)            |
| Files | Search Engine | AI Module      |
| Queue | Cache (Redis) | Notifications  |
-----------------------------------------
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
* Advanced analytics dashboard
* Mobile app (via API layer)

---

# 🎯 Summary

This system is a **complete enterprise-grade document management platform** featuring:

✔ Offline-capable UI (Tailwind CSS + Alpine.js + Flowbite, no CDN)
✔ Local file storage (XAMPP)
✔ Secure role-based authentication (RBAC) with defined Editor permissions
✔ Hybrid search engine (keyword + full-text + AI vector search)
✔ Combinable search filters (type, date, tags, category, uploader)
✔ File preview system (PDF.js)
✔ Document version control with SHA256 integrity per version
✔ Folder / category hierarchy
✔ Bulk operations (upload, delete, download, tagging)
✔ Soft delete with restorable Trash
✔ Document sharing with signed time-limited links
✔ Background job processing (queues)
✔ Advanced analytics & tagging
✔ AI-ready architecture (RAG + embeddings)
✔ In-app + email notification system
✔ REST API layer (Laravel Sanctum)
✔ Redis caching layer

---

🚀 This project is now architected as a **full SaaS-level document intelligence platform**, not just a file manager.
