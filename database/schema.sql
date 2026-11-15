mysql -u root -p < database/schema.sql


CREATE DATABASE IF NOT EXISTS google_welcome
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE google_welcome;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    google_sub VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    avatar TEXT NULL,

    welcome_email_status ENUM(
        'pending',
        'sending',
        'sent',
        'failed'
    ) NOT NULL DEFAULT 'pending',

    welcome_email_sent_at TIMESTAMP NULL,
    last_login_at TIMESTAMP NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_welcome_status (welcome_email_status)
);
