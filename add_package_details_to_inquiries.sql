-- ==========================================
-- Add Package Details Fields to Inquiries Table
-- Date: November 4, 2025
-- Purpose: Support package-specific inquiries with full package details
-- ==========================================

USE marwah_travels;

ALTER TABLE `inquiries`
ADD COLUMN `package_name` VARCHAR(255) NULL AFTER `message`,
ADD COLUMN `price_double` VARCHAR(255) NULL AFTER `package_name`,
ADD COLUMN `price_triple` VARCHAR(255) NULL AFTER `price_double`,
ADD COLUMN `price_quad` VARCHAR(255) NULL AFTER `price_triple`,
ADD COLUMN `currency` VARCHAR(255) NULL AFTER `price_quad`,
ADD COLUMN `nights_makkah` VARCHAR(255) NULL AFTER `currency`,
ADD COLUMN `nights_madina` VARCHAR(255) NULL AFTER `nights_makkah`,
ADD COLUMN `total_nights` VARCHAR(255) NULL AFTER `nights_madina`,
ADD COLUMN `hotel_makkah_name` VARCHAR(255) NULL AFTER `total_nights`,
ADD COLUMN `hotel_madina_name` VARCHAR(255) NULL AFTER `hotel_makkah_name`,
ADD COLUMN `transportation_title` VARCHAR(255) NULL AFTER `hotel_madina_name`,
ADD COLUMN `visa_title` VARCHAR(255) NULL AFTER `transportation_title`,
ADD COLUMN `breakfast_included` TINYINT(1) NULL AFTER `visa_title`,
ADD COLUMN `dinner_included` TINYINT(1) NULL AFTER `breakfast_included`,
ADD COLUMN `visa_included` TINYINT(1) NULL AFTER `dinner_included`,
ADD COLUMN `ticket_included` TINYINT(1) NULL AFTER `visa_included`,
ADD COLUMN `roundtrip` TINYINT(1) NULL AFTER `ticket_included`,
ADD COLUMN `ziyarat_included` TINYINT(1) NULL AFTER `roundtrip`,
ADD COLUMN `guide_included` TINYINT(1) NULL AFTER `ziyarat_included`;

-- Verify the changes
DESCRIBE inquiries;

-- Success message
SELECT 'Package details columns added to inquiries table successfully!' AS Status;

