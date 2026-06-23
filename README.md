# 📚 Document Management System (Laravel + MySQL)

A role-based web application built with **Laravel (PHP)** and **MySQL**, allowing administrators to manage digital resources (PDFs, images, documents) while users can securely view and download them.

---

# 🚀 Features

## 👤 Authentication

* User login and registration
* Role-based access control:

  * **Admin**
  * **User**
* Session-based authentication (Laravel default)

---

## 🛠️ Admin Features

* Upload resources (PDF, DOCX, images, etc.)
* Edit resource details
* Delete resources
* Manage users (optional extension)
* View audit logs

---

## 👁️ User Features

* View all uploaded resources
* Download files
* Search and filter resources
* Read-only access

---

## 📂 Resource Management

* Secure file upload system
* File metadata stored in MySQL
* Files stored in server storage (`/storage/app/resources`)
* Support for:

  * PDF
  * DOC / DOCX
  * XLSX
  * JPG / PNG / JPEG

---

# 🧱 Tech Stack

* Backend: Laravel (PHP 10+ / 11+ recommended)
* Database: MySQL 8+
* Frontend: Blade Templates (or Vue optional)
* Authentication: Laravel Breeze / Laravel UI
* ORM: Eloquent
* File Storage: Local storage or AWS S3 (optional)

---

# 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── ResourceController.php
│   │   ├── UserController.php
│   │   └── AdminController.php
│   ├── Middleware/
│   │   └── AdminMiddleware.php
│
├── Models/
│   ├── User.php
│   ├── Resource.php
│   └── AuditLog.php
│
├── Services/
│   ├── FileUploadService.php
│   └── AuditService.php

resources/
├── views/
│   ├── auth/
│   ├── admin/
│   ├── user/
│   └── layouts/

storage/
└── app/
    └── resources/
```

---

# 🗄️ Database Design

## Users Table

```sql
CREATE TABLE users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

## Resources Table

```sql
CREATE TABLE resources (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,

    original_filename VARCHAR(255),
    stored_filename VARCHAR(255),

    file_type VARCHAR(50),
    file_size BIGINT,

    uploaded_by BIGINT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (uploaded_by) REFERENCES users(id)
);
```

---

## Audit Logs Table

```sql
CREATE TABLE audit_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT,
    action VARCHAR(50),
    resource_id BIGINT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

# 🔐 Authentication & Authorization

## Install Authentication

```bash
composer require laravel/breeze --dev
php artisan breeze:install
php artisan migrate
npm install && npm run dev
```

---

## Role Middleware Example

```php
public function handle($request, Closure $next)
{
    if (auth()->user()->role !== 'admin') {
        abort(403, 'Unauthorized');
    }

    return $next($request);
}
```

---

## Route Protection

```php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index']);
});
```

---

# 📤 File Upload Flow

1. Admin selects file
2. Backend validates file type & size
3. File stored in `/storage/app/resources`
4. Metadata saved in MySQL
5. Audit log created

---

## Upload Validation Example

```php
$request->validate([
    'file' => 'required|mimes:pdf,doc,docx,jpg,png|max:10240',
]);
```

---

# 📥 File Download Flow

* User clicks download
* Controller checks permission
* File served securely from storage

```php
return response()->download(storage_path('app/resources/' . $file));
```

---

# 🔎 Search Feature

Users can search by:

* Title
* File type
* Description

Example query:

```php
Resource::where('title', 'like', "%$keyword%")->get();
```

---

# 🔒 Security Features

* Password hashing (bcrypt / argon2)
* CSRF protection (Laravel default)
* Input validation
* Role-based access control
* File type restriction
* File size limits
* Protected storage access

---

# ⚙️ Environment Setup

## 1. Clone Project

```bash
git clone https://github.com/your-repo/document-system.git
cd document-system
```

---

## 2. Install Dependencies

```bash
composer install
npm install
```

---

## 3. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Update database:

```
DB_DATABASE=your_db
DB_USERNAME=root
DB_PASSWORD=
```

---

## 4. Run Migrations

```bash
php artisan migrate
```

---

## 5. Start Server

```bash
php artisan serve
```

---

# 📦 Future Improvements

* File versioning system
* Full-text search (ElasticSearch)
* Cloud storage (AWS S3)
* Two-factor authentication
* Activity dashboard analytics
* API for mobile app
* AI document summarization

---

# 👨‍💻 License

MIT License

---

# 🎯 Summary

This system is a secure, scalable **document management platform** built with Laravel and MySQL, supporting role-based access control and safe file handling.
