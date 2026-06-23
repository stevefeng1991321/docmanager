# 📚 Document Management System (PHP + Laravel + XAMPP + MySQL)

A **secure, scalable, enterprise-ready document management system** built with **Laravel** running on **XAMPP (Apache + MySQL)**. It supports advanced search (keyword + AI semantic search), version control, secure file handling, preview system, analytics, and extensible AI-powered document intelligence.

All files are stored locally on the server machine (no cloud dependency).

---

# 🚀 Core Features

## 👤 Authentication & Authorization

* User login & registration
* Session-based authentication (Laravel Breeze)
* Role-based + Permission-based access control (RBAC)

  * Admin
  * Editor (optional)
  * Viewer (optional)

### Permissions include:

* Upload / edit / delete resources
* View / download resources
* Manage users
* View analytics

---

# 🛠️ Admin Features

* Upload documents (PDF, DOCX, images, etc.)
* Update / delete resources
* Manage users and roles
* View audit logs & activity tracking
* Re-index search engine
* Manage document versions

---

# 👁️ User Features

* View resources
* Download securely
* Search documents (keyword + semantic + filters)
* Preview files (PDF viewer)
* Highlight search results
* Read-only access

---

# 📁 File Storage (Local XAMPP)

All files are stored locally:

```
storage/app/resources/
```

* No cloud dependency
* Secure controller-based access
* No direct public file exposure

---

# 🔐 Security Architecture

## File Access Protection

* No direct `/storage` access
* Download via controller with permission check
* Signed / validated download requests

## Password Security

* bcrypt / argon2 hashing

## System Security

* CSRF protection (Laravel default)
* Input validation
* SQL injection protection (Eloquent ORM)
* File type + size validation

---

## File Integrity

* SHA256 file hashing
* Duplicate detection
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
| `tags` | Tag definitions |
| `resource_tags` | Many-to-many pivot: resources ↔ tags |
| `audit_logs` | Admin/system-level actions (upload, delete, permission changes) |
| `activity_logs` | User session events (login, logout, failed access) |
| `search_logs` | Every search query with type and result count |
| `download_logs` | Per-file download records |
| `resource_embeddings` | AI vector chunks per document for semantic search |
| `notifications` | Per-user notifications (upload, version update, access denied) |

---

# 🔎 Advanced Search System

The system uses a **multi-layer hybrid search engine**.

---

## 1. Metadata Search (Fast)

Search:

* file name
* title
* description

---

## 2. Full-Text Search (MySQL)

```sql
ALTER TABLE resources ADD FULLTEXT(title, description, content);
```

---

## 3. File Content Search

Extracted from:

* PDF
* DOCX
* TXT

Stored in:

```
content (LONGTEXT)
```

---

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

---

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

## 🔎 Search Highlighting

* Highlights matched keywords in:

  * title
  * description
  * preview text

---

# 📄 File Content Extraction

Supported formats:

* PDF
* DOCX
* TXT

### Libraries:

```bash
composer require smalot/pdfparser
composer require phpoffice/phpword
```

---

# 👁️ File Preview System

## PDF Viewer

* PDF.js integration (recommended)
* Browser-based preview

```
<iframe src="/file-preview/{id}"></iframe>
```

### Supported:

* PDF (full preview)
* Images
* DOCX (converted text preview)

---

# 🔄 Document Version Control

## Features:

* Maintain multiple file versions
* Restore previous versions
* Track history

## Flow:

```
Upload new version
   ↓
Store file
   ↓
Create version entry
   ↓
Keep previous versions
```

---

# ⚙️ Background Processing (Queues)

Used for:

* File content extraction
* Embedding generation
* Search indexing

```
Upload → Queue Job → Process → Store index
```

Laravel Queues:

* database / Redis

---

# 🧠 AI Semantic Search (RAG-Ready)

### Pipeline:

* Extract text
* Chunk documents
* Generate embeddings
* Store in vector DB
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

Improves search accuracy:

* finance
* legal
* invoice
* report

---

# 🔔 Notification System

* File uploaded
* Version updated
* Access denied alerts

---

# 📱 API Layer (Future-ready)

```
/api/resources
/api/search
/api/auth
```

Enables mobile apps and integrations.

---

# 🧊 Caching Layer

* Cache search results
* Cache embeddings
* Reduce DB load

---

# 🧬 OCR (Optional Upgrade)

For scanned PDFs:

* Extract text from images inside PDFs
* Enables full search support

---

# ⚡ Elasticsearch Integration (Optional)

For large-scale deployments:

* Fast indexing
* Advanced ranking
* Auto-suggestions

---

# 🧱 Tech Stack

* Backend: Laravel (PHP 10+ / 11+)
* Server: Apache (XAMPP)
* Database: MySQL 8
* Frontend: Blade Templates
* Search:

  * MySQL FULLTEXT
  * AI Vector Search
  * Hybrid Ranking Engine
* Storage: Local Disk

---

# ⚙️ System Architecture

```
Frontend (Blade)
      ↓
Laravel Backend
      ↓
-------------------------
| Auth | RBAC | API     |
| Files | Search Engine |
| AI Module | Queue     |
-------------------------
      ↓
MySQL + Local Storage + Vector DB
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

---

# 🎯 Summary

This system is a **complete enterprise-grade document management platform** featuring:

✔ Local file storage (XAMPP)
✔ Secure role-based authentication (RBAC)
✔ Hybrid search engine (keyword + full-text + AI vector search)
✔ File preview system (PDF.js)
✔ Document version control
✔ Background job processing (queues)
✔ Advanced analytics & tagging
✔ AI-ready architecture (RAG + embeddings)
✔ Extensible API layer

---

🚀 This project is now architected as a **full SaaS-level document intelligence platform**, not just a file manager.
