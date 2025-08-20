-- CSA Website Database Schema
-- MySQL/MariaDB version

CREATE DATABASE IF NOT EXISTS csa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE csa;

-- Members table
CREATE TABLE members (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name    VARCHAR(80)  NOT NULL,
  last_name     VARCHAR(80)  NOT NULL,
  email         VARCHAR(190) NOT NULL UNIQUE,
  major         VARCHAR(120) NULL,
  campus        VARCHAR(120) NULL,
  consent_comms TINYINT(1)   NOT NULL DEFAULT 0,
  accepted_code TINYINT(1)   NOT NULL DEFAULT 0,
  status        ENUM('PENDING','VERIFIED','BLOCKED') NOT NULL DEFAULT 'PENDING',
  verification_token  CHAR(64) NULL,
  verified_at   DATETIME NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_status (status),
  INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admins table
CREATE TABLE admins (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email      VARCHAR(190) NOT NULL UNIQUE,
  pass_hash  CHAR(60)     NOT NULL, -- bcrypt
  role       ENUM('OFFICER','PRESIDENT','ADVISOR') NOT NULL DEFAULT 'OFFICER',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Events table
CREATE TABLE events (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title      VARCHAR(200) NOT NULL,
  summary    TEXT NULL,
  start_time DATETIME NOT NULL,
  end_time   DATETIME NULL,
  location   VARCHAR(200) NULL,
  rsvp_url   VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_start_time (start_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rate limiting table for security
CREATE TABLE rate_limits (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ip_address VARCHAR(45) NOT NULL,
  email      VARCHAR(190) NULL,
  endpoint   VARCHAR(50) NOT NULL,
  attempts   INT UNSIGNED NOT NULL DEFAULT 1,
  last_attempt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ip_endpoint (ip_address, endpoint),
  INDEX idx_email_endpoint (email, endpoint),
  INDEX idx_last_attempt (last_attempt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
