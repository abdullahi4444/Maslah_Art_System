-- Maslah Arts Database Setup
-- Created: 8/23/2025

-- Create database
CREATE DATABASE IF NOT EXISTS maslah_arts;
USE maslah_arts;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME NULL
);

-- Indexes for users table
CREATE INDEX idx_username ON users(username);
CREATE INDEX idx_email ON users(email);
CREATE INDEX idx_is_active ON users(is_active);

-- Password reset tokens table
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_used TINYINT(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_token ON password_reset_tokens(token);
CREATE INDEX idx_expires_at ON password_reset_tokens(expires_at);

-- Login attempts table
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    ip_address VARCHAR(45) NOT NULL,
    username VARCHAR(255) NOT NULL,
    attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_successful TINYINT(1) DEFAULT 0
);

CREATE INDEX idx_ip_address ON login_attempts(ip_address);
CREATE INDEX idx_attempt_time ON login_attempts(attempt_time);

-- User profiles table
CREATE TABLE IF NOT EXISTS user_profiles (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL UNIQUE,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    avatar_url VARCHAR(255),
    bio TEXT,
    date_of_birth DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create database user with limited privileges
CREATE USER IF NOT EXISTS 'maslah_arts_user'@'localhost' IDENTIFIED BY 'SecurePass123!';

-- Grant necessary privileges
GRANT SELECT, INSERT, UPDATE ON maslah_arts.users TO 'maslah_arts_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON maslah_arts.password_reset_tokens TO 'maslah_arts_user'@'localhost';
GRANT INSERT ON maslah_arts.login_attempts TO 'maslah_arts_user'@'localhost';
GRANT SELECT, INSERT, UPDATE ON maslah_arts.user_profiles TO 'maslah_arts_user'@'localhost';

FLUSH PRIVILEGES;

-- Display confirmation
SELECT 'Database setup completed successfully!' AS status;
SELECT 'Database: maslah_arts' AS info;
SELECT 'Application User: maslah_arts_user' AS info;
SELECT 'Tables created: users, password_reset_tokens, login_attempts, user_profiles' AS info;



-- Add profile_image column to users table
ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT 'assets/image/icon.png';

-- Update the existing records to use the default profile image
UPDATE users SET profile_image = 'assets/image/icon.png' WHERE profile_image IS NULL;

-- Display confirmation
SELECT 'Column profile_image added to users table successfully!' AS status;



-- Add verification_code column to password_reset_tokens table
ALTER TABLE password_reset_tokens ADD COLUMN verification_code VARCHAR(6) NULL;

-- Add index for faster lookups
CREATE INDEX idx_verification_code ON password_reset_tokens(verification_code);



ALTER TABLE user_profiles 
ADD COLUMN twitter_link VARCHAR(255) NULL AFTER bio,
ADD COLUMN facebook_link VARCHAR(255) NULL AFTER twitter_link,
ADD COLUMN linkedin_link VARCHAR(255) NULL AFTER facebook_link;