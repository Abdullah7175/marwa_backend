#!/bin/bash
# Script to apply server-side fixes for Marwah Travels Backend
# Run this on your server after deploying the updated code

set -e  # Exit on error

echo "=========================================="
echo "Marwah Travels - Server Fixes Application"
echo "=========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Database credentials - CHANGE THESE
DB_NAME="marwah_travels"
DB_USER="root"
DB_PASS=""

echo -e "${YELLOW}Step 1: Backing up current database...${NC}"
BACKUP_FILE="backup_$(date +%Y%m%d_%H%M%S).sql"
mysqldump -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_FILE" 2>/dev/null || {
    echo -e "${RED}Failed to create backup. Please check database credentials.${NC}"
    echo "Update DB_USER and DB_PASS in this script."
    exit 1
}
echo -e "${GREEN}✓ Backup created: $BACKUP_FILE${NC}"
echo ""

echo -e "${YELLOW}Step 2: Applying hotels table migration...${NC}"
if [ -f "fix_hotels_table.sql" ]; then
    mysql -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < fix_hotels_table.sql 2>/dev/null || {
        echo -e "${RED}Failed to apply migration. Check fix_hotels_table.sql${NC}"
        exit 1
    }
    echo -e "${GREEN}✓ Hotels table migration applied${NC}"
else
    echo -e "${YELLOW}⚠ fix_hotels_table.sql not found. Running inline SQL...${NC}"
    mysql -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" <<EOF
ALTER TABLE \`hotels\` 
ADD COLUMN \`currency\` VARCHAR(255) NULL DEFAULT 'USD' AFTER \`description\`,
ADD COLUMN \`phone\` VARCHAR(255) NULL AFTER \`currency\`,
ADD COLUMN \`email\` VARCHAR(255) NULL AFTER \`phone\`,
ADD COLUMN \`status\` VARCHAR(255) NULL DEFAULT 'active' AFTER \`email\`,
ADD COLUMN \`breakfast_enabled\` TINYINT(1) DEFAULT 0 AFTER \`status\`,
ADD COLUMN \`dinner_enabled\` TINYINT(1) DEFAULT 0 AFTER \`breakfast_enabled\`;
EOF
    echo -e "${GREEN}✓ Inline migration applied${NC}"
fi
echo ""

echo -e "${YELLOW}Step 3: Checking PHP configuration...${NC}"
PHP_INI="/etc/php/8.2/fpm/php.ini"
if [ -f "$PHP_INI" ]; then
    echo "Current PHP upload limits:"
    grep -E "(upload_max_filesize|post_max_size|memory_limit)" "$PHP_INI" | head -3
    echo ""
    echo -e "${YELLOW}⚠ Please manually update $PHP_INI with:${NC}"
    echo "  upload_max_filesize = 50M"
    echo "  post_max_size = 50M"
    echo "  memory_limit = 512M"
    echo ""
    echo -e "${YELLOW}Then restart PHP-FPM:${NC}"
    echo "  sudo systemctl restart php8.2-fpm"
else
    echo -e "${RED}PHP config not found at $PHP_INI${NC}"
fi
echo ""

echo -e "${YELLOW}Step 4: Checking Nginx configuration...${NC}"
NGINX_SITE="/etc/nginx/sites-available/mtumrah"
if [ -f "$NGINX_SITE" ]; then
    echo "Please review and update Nginx config at: $NGINX_SITE"
    echo "Reference: nginx_config_fix.txt"
    echo ""
    echo -e "${YELLOW}After updating, run:${NC}"
    echo "  sudo nginx -t && sudo systemctl reload nginx"
else
    echo -e "${RED}Nginx config not found at $NGINX_SITE${NC}"
fi
echo ""

echo -e "${YELLOW}Step 5: Verifying database structure...${NC}"
mysql -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "DESCRIBE hotels;" 2>/dev/null || {
    echo -e "${RED}Failed to verify hotels table${NC}"
    exit 1
}
echo -e "${GREEN}✓ Hotels table structure verified${NC}"
echo ""

echo -e "${GREEN}=========================================="
echo "Database migration completed successfully!"
echo "==========================================${NC}"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo "1. Update PHP limits in $PHP_INI"
echo "2. Restart PHP-FPM: sudo systemctl restart php8.2-fpm"
echo "3. Update Nginx config (see nginx_config_fix.txt)"
echo "4. Reload Nginx: sudo nginx -t && sudo systemctl reload nginx"
echo "5. Clear Laravel cache: php artisan config:cache"
echo "6. Test all create/edit forms"
echo ""
echo -e "${GREEN}Backup saved as: $BACKUP_FILE${NC}"

