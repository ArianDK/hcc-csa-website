-- CSA Website Database Schema
-- SQLite version

-- Members table
CREATE TABLE members (
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  first_name    TEXT NOT NULL,
  last_name     TEXT NOT NULL,
  email         TEXT NOT NULL UNIQUE,
  major         TEXT,
  campus        TEXT,
  consent_comms INTEGER NOT NULL DEFAULT 0,
  accepted_code INTEGER NOT NULL DEFAULT 0,
  status        TEXT NOT NULL DEFAULT 'PENDING' CHECK (status IN ('PENDING','VERIFIED','BLOCKED')),
  verification_token  TEXT,
  verified_at   DATETIME,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_members_email ON members(email);
CREATE INDEX idx_members_status ON members(status);
CREATE INDEX idx_members_created_at ON members(created_at);

-- Trigger for updated_at
CREATE TRIGGER members_updated_at 
  AFTER UPDATE ON members
  BEGIN
    UPDATE members SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
  END;

-- Admins table
CREATE TABLE admins (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  email      TEXT NOT NULL UNIQUE,
  pass_hash  TEXT NOT NULL,
  role       TEXT NOT NULL DEFAULT 'OFFICER' CHECK (role IN ('OFFICER','PRESIDENT','ADVISOR')),
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_admins_email ON admins(email);

-- Events table
CREATE TABLE events (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  title      TEXT NOT NULL,
  summary    TEXT,
  start_time DATETIME NOT NULL,
  end_time   DATETIME,
  location   TEXT,
  rsvp_url   TEXT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_events_start_time ON events(start_time);

-- Rate limiting table for security
CREATE TABLE rate_limits (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  ip_address TEXT NOT NULL,
  email      TEXT,
  endpoint   TEXT NOT NULL,
  attempts   INTEGER NOT NULL DEFAULT 1,
  last_attempt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_rate_limits_ip_endpoint ON rate_limits(ip_address, endpoint);
CREATE INDEX idx_rate_limits_email_endpoint ON rate_limits(email, endpoint);
CREATE INDEX idx_rate_limits_last_attempt ON rate_limits(last_attempt);
