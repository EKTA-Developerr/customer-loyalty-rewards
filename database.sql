-- FreshBite Customer Loyalty QR Cashback System
-- Database Schema & Initial Seed Data (MySQL / MariaDB)

CREATE DATABASE IF NOT EXISTS freshbite_loyalty CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE freshbite_loyalty;

CREATE TABLE IF NOT EXISTS batches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    batch_name VARCHAR(100) NOT NULL,
    cashback_amount INT NOT NULL DEFAULT 25,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS customers (
    phone VARCHAR(20) PRIMARY KEY,
    total_scans INT NOT NULL DEFAULT 0,
    total_earned INT NOT NULL DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS product_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    batch_id INT NOT NULL,
    code VARCHAR(32) NOT NULL UNIQUE,
    cashback_amount INT NOT NULL DEFAULT 25,
    bonus_amount INT NOT NULL DEFAULT 0,
    final_payout INT NOT NULL DEFAULT 0,
    status ENUM('UNUSED', 'REDEEMED') NOT NULL DEFAULT 'UNUSED',
    redeemed_phone VARCHAR(20) DEFAULT NULL,
    redeemed_upi VARCHAR(100) DEFAULT NULL,
    redeemed_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_batch FOREIGN KEY (batch_id) REFERENCES batches(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS fraud_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code_attempted VARCHAR(64) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    reason VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS rate_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(120) NOT NULL UNIQUE,
    failed_attempts INT NOT NULL DEFAULT 0,
    locked_until DATETIME NULL DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Seed initial batches and sample codes
INSERT INTO batches (id, batch_name, cashback_amount, quantity) VALUES
(1, 'Batch-Nov-01', 25, 3),
(2, 'Batch-Oct-09', 25, 2)
ON DUPLICATE KEY UPDATE batch_name = VALUES(batch_name);

INSERT IGNORE INTO product_codes (batch_id, code, cashback_amount, bonus_amount, final_payout, status, redeemed_phone, redeemed_upi, redeemed_at) VALUES
(1, 'FB-9X4K-2M8P', 25, 0, 0, 'UNUSED', NULL, NULL, NULL),
(1, 'FB-7L2Q-9R1A', 50, 0, 0, 'UNUSED', NULL, NULL, NULL),
(1, 'FB-4T8W-6C3N', 25, 0, 0, 'UNUSED', NULL, NULL, NULL),
(2, 'FB-USED-0001', 25, 0, 25, 'REDEEMED', '9825011223', 'amit@ybl', NOW()),
(2, 'FB-USED-0002', 50, 0, 50, 'REDEEMED', '9911223344', 'neha@oksbi', NOW());

-- Seed demo customers (9999999999 starts at 99 scans so 100th scan unlocks Platinum VIP 2x)
INSERT IGNORE INTO customers (phone, total_scans, total_earned) VALUES
('9825011223', 1, 25),
('9911223344', 1, 50),
('9888888888', 9, 225),
('9777777777', 49, 1225),
('9999999999', 99, 2475);

INSERT INTO fraud_logs (code_attempted, phone, ip_address, reason) VALUES
('FB-USED-0001', '9811002233', '127.0.0.1', 'Duplicate reuse attempt blocked'),
('FAKE-1122', '9700112233', '127.0.0.1', 'Invalid code guessed (Rate-limit warning)');
