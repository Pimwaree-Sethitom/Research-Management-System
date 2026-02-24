-- ==========================================
-- Research Management System (ICT Faculty)
-- MySQL 8.0 Schema
-- ==========================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ==========================================
-- Core Tables
-- ==========================================

CREATE TABLE researchers (
    researcher_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    department_name_en VARCHAR(255),
    department_name_th VARCHAR(255)
) ENGINE=InnoDB;

-- ------------------------------------------

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    researcher_id INT,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_users_researcher
        FOREIGN KEY (researcher_id)
        REFERENCES researchers(researcher_id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------

CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ------------------------------------------

CREATE TABLE user_roles (
    user_id INT,
    role_id INT,
    PRIMARY KEY (user_id, role_id),

    CONSTRAINT fk_user_roles_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_user_roles_role
        FOREIGN KEY (role_id)
        REFERENCES roles(role_id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------

CREATE TABLE auth_tokens (
    token_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    access_token VARCHAR(500) NOT NULL,
    expires_at DATETIME NOT NULL,
    revoked_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_auth_tokens_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE,

    INDEX idx_access_token (access_token)
) ENGINE=InnoDB;

-- ==========================================
-- Reference Tables
-- ==========================================

CREATE TABLE research_types (
    research_type_id INT AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- ------------------------------------------

CREATE TABLE quartiles (
    quartile_id INT AUTO_INCREMENT PRIMARY KEY,
    quartile_rank VARCHAR(10) NOT NULL
) ENGINE=InnoDB;

-- ------------------------------------------

CREATE TABLE workload_definitions (
    workload_definition_id INT AUTO_INCREMENT PRIMARY KEY,
    topic VARCHAR(255) NOT NULL,
    workload_score DECIMAL(10,2) NOT NULL,
    applicable_year VARCHAR(20)
) ENGINE=InnoDB;

-- ==========================================
-- Publication Section
-- ==========================================

CREATE TABLE publications (
    publication_id INT AUTO_INCREMENT PRIMARY KEY,
    title_th VARCHAR(500),
    title_en VARCHAR(500),
    research_type_id INT,
    venue_name VARCHAR(255),
    publication_year YEAR,
    issue_number VARCHAR(100),
    publish_start_date DATE,
    publish_end_date DATE,
    issn_isbn VARCHAR(100),
    page_range VARCHAR(100),
    quartile_id INT,
    academic_quality VARCHAR(255),
    remark TEXT,
    abstract TEXT,
    reference_url VARCHAR(500),

    CONSTRAINT fk_publications_research_type
        FOREIGN KEY (research_type_id)
        REFERENCES research_types(research_type_id)
        ON DELETE SET NULL,

    CONSTRAINT fk_publications_quartile
        FOREIGN KEY (quartile_id)
        REFERENCES quartiles(quartile_id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------

CREATE TABLE publication_authors (
    publication_author_id INT AUTO_INCREMENT PRIMARY KEY,
    publication_id INT NOT NULL,
    researcher_id INT NOT NULL,
    author_role VARCHAR(100),
    workload_definition_id INT,
    contribution_ratio DECIMAL(5,2),
    workload_amount DECIMAL(10,2),
    fiscal_year YEAR,
    academic_year YEAR,

    CONSTRAINT fk_pub_authors_publication
        FOREIGN KEY (publication_id)
        REFERENCES publications(publication_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_pub_authors_researcher
        FOREIGN KEY (researcher_id)
        REFERENCES researchers(researcher_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_pub_authors_workload
        FOREIGN KEY (workload_definition_id)
        REFERENCES workload_definitions(workload_definition_id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;