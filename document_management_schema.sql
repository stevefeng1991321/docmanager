-- Document Management System — MySQL 8.0+
-- All tables use InnoDB with UTF-8 encoding.
-- Creation order respects foreign key dependencies.

SET NAMES utf8mb4;
SET foreign_key_checks = 0;

-- ---------------------------------------------------------------------------
-- users
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id                          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username                    VARCHAR(50)     NOT NULL UNIQUE,
    name                        VARCHAR(255)    NOT NULL,
    email                       VARCHAR(255)    NULL UNIQUE,    -- optional; used for notifications only
    password                    VARCHAR(255)    NOT NULL,
    role                        ENUM('admin','editor','viewer') NOT NULL DEFAULT 'viewer',
    status                      ENUM('pending','active','inactive') NOT NULL DEFAULT 'pending',
    failed_login_attempts       TINYINT UNSIGNED NOT NULL DEFAULT 0,
    locked_until                TIMESTAMP       NULL,
    two_factor_secret           VARCHAR(255)    NULL,
    two_factor_recovery_codes   TEXT            NULL,
    last_login_at               TIMESTAMP       NULL,
    remember_token              VARCHAR(100),
    created_at                  TIMESTAMP       NULL,
    updated_at                  TIMESTAMP       NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- personal_access_tokens  (Laravel Sanctum)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS personal_access_tokens (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tokenable_type  VARCHAR(255)    NOT NULL,
    tokenable_id    BIGINT UNSIGNED NOT NULL,
    name            VARCHAR(255)    NOT NULL,
    token           VARCHAR(64)     NOT NULL UNIQUE,
    abilities       TEXT,
    last_used_at    TIMESTAMP       NULL,
    expires_at      TIMESTAMP       NULL,
    created_at      TIMESTAMP       NULL,
    updated_at      TIMESTAMP       NULL,
    KEY idx_tokenable (tokenable_type, tokenable_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- categories  (hierarchical tree; self-referential FK)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)    NOT NULL,
    slug        VARCHAR(100)    NOT NULL UNIQUE,
    parent_id   BIGINT UNSIGNED NULL,
    sort_order  INT UNSIGNED    NOT NULL DEFAULT 0,
    created_at  TIMESTAMP       NULL,
    updated_at  TIMESTAMP       NULL,
    CONSTRAINT fk_cat_parent FOREIGN KEY (parent_id)
        REFERENCES categories (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- resources
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS resources (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title               VARCHAR(255)    NOT NULL,
    description         TEXT,
    original_filename   VARCHAR(255)    NOT NULL,
    stored_filename     VARCHAR(255)    NOT NULL,
    file_path           VARCHAR(500)    NOT NULL,
    file_type           VARCHAR(100)    NOT NULL,
    file_size           BIGINT UNSIGNED NOT NULL,
    file_hash           VARCHAR(64),
    content             LONGTEXT,
    category_id         BIGINT UNSIGNED NULL,
    uploaded_by         BIGINT UNSIGNED NOT NULL,
    status              ENUM('draft','pending_review','published','rejected') NOT NULL DEFAULT 'draft',
    locked_by           BIGINT UNSIGNED NULL,
    locked_at           TIMESTAMP       NULL,
    download_count      INT UNSIGNED    NOT NULL DEFAULT 0,
    expires_at          TIMESTAMP       NULL,
    deleted_at          TIMESTAMP       NULL,
    created_at          TIMESTAMP       NULL,
    updated_at          TIMESTAMP       NULL,
    FULLTEXT KEY ft_search (title, description, content),
    CONSTRAINT fk_resources_user FOREIGN KEY (uploaded_by)
        REFERENCES users (id) ON DELETE RESTRICT,
    CONSTRAINT fk_resources_category FOREIGN KEY (category_id)
        REFERENCES categories (id) ON DELETE SET NULL,
    CONSTRAINT fk_resources_locked_by FOREIGN KEY (locked_by)
        REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- document_versions
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS document_versions (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    resource_id     BIGINT UNSIGNED NOT NULL,
    version_number  INT UNSIGNED    NOT NULL DEFAULT 1,
    file_path       VARCHAR(500)    NOT NULL,
    stored_filename VARCHAR(255)    NOT NULL,
    file_size       BIGINT UNSIGNED NOT NULL,
    file_hash       VARCHAR(64),
    change_note     TEXT            NULL,
    uploaded_by     BIGINT UNSIGNED NOT NULL,
    created_at      TIMESTAMP       NULL,
    UNIQUE KEY uq_version (resource_id, version_number),
    CONSTRAINT fk_versions_resource FOREIGN KEY (resource_id)
        REFERENCES resources (id) ON DELETE CASCADE,
    CONSTRAINT fk_versions_user FOREIGN KEY (uploaded_by)
        REFERENCES users (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- document_access_logs  (per-document view and download tracking)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS document_access_logs (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    resource_id BIGINT UNSIGNED NOT NULL,
    version_id  BIGINT UNSIGNED NULL,
    user_id     BIGINT UNSIGNED NULL,
    action      ENUM('view','download') NOT NULL DEFAULT 'view',
    ip_address  VARCHAR(45),
    created_at  TIMESTAMP       NULL,
    CONSTRAINT fk_docaccess_resource FOREIGN KEY (resource_id)
        REFERENCES resources (id) ON DELETE CASCADE,
    CONSTRAINT fk_docaccess_version FOREIGN KEY (version_id)
        REFERENCES document_versions (id) ON DELETE SET NULL,
    CONSTRAINT fk_docaccess_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- tags
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tags (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL UNIQUE,
    slug        VARCHAR(100) NOT NULL UNIQUE,
    created_at  TIMESTAMP    NULL,
    updated_at  TIMESTAMP    NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- resource_tags  (many-to-many pivot)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS resource_tags (
    resource_id BIGINT UNSIGNED NOT NULL,
    tag_id      BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (resource_id, tag_id),
    CONSTRAINT fk_rt_resource FOREIGN KEY (resource_id)
        REFERENCES resources (id) ON DELETE CASCADE,
    CONSTRAINT fk_rt_tag FOREIGN KEY (tag_id)
        REFERENCES tags (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- favorites
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS favorites (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL,
    resource_id BIGINT UNSIGNED NOT NULL,
    created_at  TIMESTAMP       NULL,
    UNIQUE KEY uq_fav (user_id, resource_id),
    CONSTRAINT fk_fav_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_fav_resource FOREIGN KEY (resource_id)
        REFERENCES resources (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- recently_viewed  (capped at 50 entries per user via application logic)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS recently_viewed (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL,
    resource_id BIGINT UNSIGNED NOT NULL,
    viewed_at   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_rv (user_id, resource_id),
    CONSTRAINT fk_rv_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_rv_resource FOREIGN KEY (resource_id)
        REFERENCES resources (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- saved_searches
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS saved_searches (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL,
    name        VARCHAR(100)    NOT NULL,
    query       VARCHAR(500)    NOT NULL,
    filters     JSON,
    created_at  TIMESTAMP       NULL,
    updated_at  TIMESTAMP       NULL,
    CONSTRAINT fk_ss_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- shares  (signed time-limited share links)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS shares (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    resource_id BIGINT UNSIGNED NOT NULL,
    created_by  BIGINT UNSIGNED NOT NULL,
    token       VARCHAR(64)     NOT NULL UNIQUE,
    expires_at  TIMESTAMP       NOT NULL,
    revoked_at  TIMESTAMP       NULL,
    created_at  TIMESTAMP       NULL,
    CONSTRAINT fk_share_resource FOREIGN KEY (resource_id)
        REFERENCES resources (id) ON DELETE CASCADE,
    CONSTRAINT fk_share_user FOREIGN KEY (created_by)
        REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- audit_logs  (admin/system-level actions)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS audit_logs (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED,
    action      VARCHAR(100)    NOT NULL,
    resource_id BIGINT UNSIGNED,
    details     JSON,
    ip_address  VARCHAR(45),
    created_at  TIMESTAMP       NULL,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE SET NULL,
    CONSTRAINT fk_audit_resource FOREIGN KEY (resource_id)
        REFERENCES resources (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- activity_logs  (session events: login, logout, failed access, lockouts)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS activity_logs (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED,
    event       VARCHAR(100)    NOT NULL,
    ip_address  VARCHAR(45),
    user_agent  TEXT,
    details     JSON,
    created_at  TIMESTAMP       NULL,
    CONSTRAINT fk_activity_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- search_logs
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS search_logs (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED,
    query           VARCHAR(500)    NOT NULL,
    search_type     ENUM('keyword','fulltext','semantic','hybrid') NOT NULL DEFAULT 'hybrid',
    results_count   INT UNSIGNED    NOT NULL DEFAULT 0,
    created_at      TIMESTAMP       NULL,
    CONSTRAINT fk_searchlog_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- download_logs
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS download_logs (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED,
    resource_id BIGINT UNSIGNED,
    ip_address  VARCHAR(45),
    created_at  TIMESTAMP NULL,
    CONSTRAINT fk_dllog_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE SET NULL,
    CONSTRAINT fk_dllog_resource FOREIGN KEY (resource_id)
        REFERENCES resources (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- resource_embeddings  (AI vector chunks; use Qdrant/Chroma for production)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS resource_embeddings (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    resource_id BIGINT UNSIGNED NOT NULL,
    chunk_index INT UNSIGNED    NOT NULL DEFAULT 0,
    chunk_text  TEXT            NOT NULL,
    embedding   JSON            NOT NULL,
    model       VARCHAR(100)    NOT NULL DEFAULT 'text-embedding-ada-002',
    created_at  TIMESTAMP       NULL,
    updated_at  TIMESTAMP       NULL,
    UNIQUE KEY uq_resource_chunk (resource_id, chunk_index),
    CONSTRAINT fk_embed_resource FOREIGN KEY (resource_id)
        REFERENCES resources (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- notifications
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS notifications (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     BIGINT UNSIGNED NOT NULL,
    type        VARCHAR(100)    NOT NULL,
    title       VARCHAR(255)    NOT NULL,
    message     TEXT,
    resource_id BIGINT UNSIGNED,
    is_read     BOOLEAN         NOT NULL DEFAULT FALSE,
    created_at  TIMESTAMP       NULL,
    CONSTRAINT fk_notif_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_notif_resource FOREIGN KEY (resource_id)
        REFERENCES resources (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- user_preferences  (per-user settings and optional profile fields)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS user_preferences (
    id                          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id                     BIGINT UNSIGNED NOT NULL UNIQUE,
    display_name                VARCHAR(100)    NULL,
    avatar                      VARCHAR(500)    NULL,
    view_mode                   ENUM('grid','list') NOT NULL DEFAULT 'grid',
    items_per_page              TINYINT UNSIGNED NOT NULL DEFAULT 20,
    notify_file_uploaded        BOOLEAN         NOT NULL DEFAULT TRUE,
    notify_version_updated      BOOLEAN         NOT NULL DEFAULT TRUE,
    notify_access_denied        BOOLEAN         NOT NULL DEFAULT TRUE,
    notify_doc_approved         BOOLEAN         NOT NULL DEFAULT TRUE,
    notify_account_activated    BOOLEAN         NOT NULL DEFAULT TRUE,
    created_at                  TIMESTAMP       NULL,
    updated_at                  TIMESTAMP       NULL,
    CONSTRAINT fk_pref_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET foreign_key_checks = 1;
