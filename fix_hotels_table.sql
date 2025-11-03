-- Migration to add missing columns to hotels table
-- Run this SQL in your database to fix the hotel creation issue

ALTER TABLE `hotels` 
ADD COLUMN `currency` VARCHAR(255) NULL DEFAULT 'USD' AFTER `description`,
ADD COLUMN `phone` VARCHAR(255) NULL AFTER `currency`,
ADD COLUMN `email` VARCHAR(255) NULL AFTER `phone`,
ADD COLUMN `status` VARCHAR(255) NULL DEFAULT 'active' AFTER `email`,
ADD COLUMN `breakfast_enabled` TINYINT(1) DEFAULT 0 AFTER `status`,
ADD COLUMN `dinner_enabled` TINYINT(1) DEFAULT 0 AFTER `breakfast_enabled`;

-- Verify the changes
DESCRIBE hotels;

