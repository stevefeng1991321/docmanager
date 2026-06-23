# 📚 Document Management System (PHP + Laravel + XAMPP + MySQL)

A secure, scalable document management system built with **Laravel** running on **XAMPP (Apache + MySQL)**. It supports role-based access, advanced search, AI semantic search, file preview, version control, and extensible enterprise-grade features — all using local storage (no cloud dependency).

---

# 🚀 Core Features

## 👤 Authentication

* User login & registration
* Role-based access control:

  * Admin
  * User
* Session-based authentication (Laravel Breeze)
* Secure password hashing (bcrypt / argon2)

---

## 🛠️ Admin Features

* Upload documents (PDF, DOCX, images, etc.)
* Update and delete resources
* Manage users
* View audit logs
* Document version control
* Re-index files for search engine

---

## 👁️ User Features

* View available resources
* Download files securely
* Search documents (basic + semantic + filters)
* Preview files (PDF viewer)
* Read-only access

---

# 📁 File Storage (Local XAMPP)

All files are stored locally:

```
storage/app/resources/
```

* No cloud storage required
* Apache serves Laravel application
* Files accessed via secure controller (not direct URL)

---

# 🔎 Advanced Search System

The system includes a **multi-layer search engine**:

---

## 1. Metadata Search (Fast)

Searches:

* File name
* Title
* Description

```php
Resource::where('title','LIKE',"%$q%")
```

---

## 2. Full-Text Search (MySQL Optimized)

```sql
ALTER TABLE resources ADD FULLTEXT(title, description, content);
```

```php
MATCH(title, description, content)
AGAINST(? IN NATURAL LANGUAGE MODE)
```

---

## 3. File Content Search

Extracts and indexes:

* PDF text
* DOCX content
* TXT files

Stored in:

```sql
content LONGTEXT
```

---

## 4. AI Semantic Search (Vector-Based)

### Architecture:

```text id="semantic1"
User Query
   │
   ▼
Embedding Model (OpenAI / Local Model)
   │
   ▼
Vector Database (Qdrant / Weaviate / Chroma)
   │
   ▼
Similarity Search (Cosine Distance)
   │
   ▼
Ranked Results
```

### Features:

* Understands meaning, not keywords
* Finds similar documents even if words differ
* Supports natural language queries

---

## 5. Hybrid Search Engine (Final System)

Search combines:

* Keyword search (MySQL)
* Full-text search
* Vector similarity search (AI)
* File content extraction

```text id="hybrid1"
User Query
   │
   ├── Keyword Search (MySQL)
   ├── Full Text Search
   ├── Vector Search (AI)
   └── Content Search
            ↓
      Result Ranking Engine
            ↓
        Final Results
```

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

* Embedded browser-based PDF viewer
* No download required for preview

Example:

```html
<iframe src="/storage/app/resources/file.pdf" width="100%" height="600px"></iframe>
```

---

## Supported Preview Types:

* PDF (full preview)
* Images (JPG/PNG)
* DOCX (converted to text preview)

---

# 📂 Document Version Control

Each document supports multiple versions:

## Schema:

```sql
document_versions (
    id,
    resource_id,
    version_number,
    file_path,
    created_at
)
```

---

## Flow:

```text id="version1"
Upload New Version
      │
      ▼
Store file as new version
      │
      ▼
Keep history intact
```

### Features:

* Restore previous versions
* Compare versions (future feature)
* Track changes over time

---

# 🔎 Search Highlighting

Search results highlight matched keywords:

```text id="highlight1"
Invoice 2024 Report
        ↑
     matched keyword
```

### Implementation:

* Wrap keywords in `<mark>` tags
* Highlight in title, description, and content preview

---

# 📊 Advanced Filters

Users can filter results by:

* File type (PDF, DOCX, Image)
* Upload date range
* Uploader (Admin/User)
* Version number
* Tags (future feature)

---

## Example Query:

```sql
SELECT * FROM resources
WHERE file_type = 'pdf'
AND uploaded_by = 1
AND created_at BETWEEN ? AND ?;
```

---

# ⚡ Elasticsearch Integration (Optional Upgrade)

For large-scale systems:

### Features:

* Fast full-text search
* Ranking optimization
* Auto-suggestions

```text id="elastic1"
Laravel → Elasticsearch → Indexed Documents → Search API
```

---

# 🧠 AI Semantic Search (Upgrade Layer)

### How it works:

* Convert documents into embeddings
* Store in vector DB
* Compare query vector with document vectors

### Tools:

* OpenAI Embeddings
* Ollama (local AI)
* Qdrant / Weaviate

---

# 🗄️ Database Schema (Extended)

## resources

```sql
content LONGTEXT,
embedding VECTOR (optional external DB)
```

---

## document_versions

```sql
id
resource_id
version_number
file_path
created_at
```

---

# 🔐 Security Features

* Role-based access control
* Secure file download (no direct access)
* CSRF protection
* File validation (mimes + size)
* SQL injection protection (Eloquent ORM)

---

# ⚙️ Tech Stack

* Backend: Laravel (PHP 10+ / 11+)
* Server: Apache (XAMPP)
* Database: MySQL 8
* Frontend: Blade Templates
* Search:

  * MySQL FULLTEXT
  * AI Vector Search
  * File content indexing
* Storage: Local Disk

---

# 🔮 Future Enhancements

* AI document summarization
* Chat with documents (RAG system)
* Multi-user collaboration
* Real-time notifications
* OCR for scanned PDFs
* Advanced analytics dashboard
* Elasticsearch production scaling

---

# 🎯 Summary

This system is a **complete enterprise-grade document management platform** featuring:

✔ Local file storage (XAMPP)
✔ Role-based authentication
✔ Admin resource management
✔ Hybrid search engine
✔ AI semantic search (vector-based)
✔ File preview system
✔ Version control
✔ Advanced filtering & highlighting
✔ Elasticsearch-ready architecture

---

🚀 This design is ready to scale from a local XAMPP project to a full enterprise SaaS system.
