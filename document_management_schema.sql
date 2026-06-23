-- Document Management System — MySQL 8.0+
-- All tables use InnoDB with UTF-8 encoding.

SET NAMES utf8mb4;
SET foreign_key_checks = 0;

-- ---------------------------------------------------------------------------
-- users
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(255)    NOT NULL,
    email           VARCHAR(255)    NOT NULL UNIQUE,
    password        VARCHAR(255)    NOT NULL,
    role            ENUM('admin','editor','viewer') NOT NULL DEFAULT 'viewer',
    is_active       BOOLEAN         NOT NULL DEFAULT TRUE,
    remember_token  VARCHAR(100),
    created_at      TIMESTAMP       NULL,
    updated_at      TIMESTAMP       NULL
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
    uploaded_by         BIGINT UNSIGNED NOT NULL,
    created_at          TIMESTAMP       NULL,
    updated_at          TIMESTAMP       NULL,
    FULLTEXT KEY ft_search (title, description, content),
    CONSTRAINT fk_resources_user FOREIGN KEY (uploaded_by)
        REFERENCES users (id) ON DELETE RESTRICT
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
    uploaded_by     BIGINT UNSIGNED NOT NULL,
    created_at      TIMESTAMP       NULL,
    CONSTRAINT fk_versions_resource FOREIGN KEY (resource_id)
        REFERENCES resources (id) ON DELETE CASCADE,
    CONSTRAINT fk_versions_user FOREIGN KEY (uploaded_by)
        REFERENCES users (id) ON DELETE RESTRICT
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
-- activity_logs  (session events: login, logout, failed access)
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
    type        VARCHAR(100)    NOT NULL,   -- file_uploaded | version_updated | access_denied
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

SET foreign_key_checks = 1;
