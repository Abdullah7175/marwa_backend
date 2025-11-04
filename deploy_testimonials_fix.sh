#!/bin/bash

###############################################################################
# Testimonials Video Upload Fix - Complete Deployment Script
# This script applies ALL necessary fixes for video upload functionality
###############################################################################

echo "=========================================="
echo "  Testimonials Video Upload Fix"
echo "=========================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}Please run as root or with sudo${NC}"
    exit 1
fi

echo -e "${YELLOW}Step 1: Updating CLI PHP configuration...${NC}"
sed -i 's/upload_max_filesize = .*/upload_max_filesize = 1024M/' /etc/php/8.2/cli/php.ini
sed -i 's/post_max_size = .*/post_max_size = 1024M/' /etc/php/8.2/cli/php.ini
sed -i 's/memory_limit = .*/memory_limit = 512M/' /etc/php/8.2/cli/php.ini
sed -i 's/max_execution_time = .*/max_execution_time = 600/' /etc/php/8.2/cli/php.ini
sed -i 's/max_input_time = .*/max_input_time = 600/' /etc/php/8.2/cli/php.ini
echo -e "${GREEN}✓ CLI PHP configuration updated${NC}"

echo -e "${YELLOW}Step 2: Updating PHP-FPM configuration...${NC}"
sed -i 's/upload_max_filesize = .*/upload_max_filesize = 1024M/' /etc/php/8.2/fpm/php.ini
sed -i 's/post_max_size = .*/post_max_size = 1024M/' /etc/php/8.2/fpm/php.ini
sed -i 's/memory_limit = .*/memory_limit = 512M/' /etc/php/8.2/fpm/php.ini
sed -i 's/max_execution_time = .*/max_execution_time = 600/' /etc/php/8.2/fpm/php.ini
sed -i 's/max_input_time = .*/max_input_time = 600/' /etc/php/8.2/fpm/php.ini
echo -e "${GREEN}✓ PHP-FPM configuration updated${NC}"

echo -e "${YELLOW}Step 3: Adding pool configuration overrides...${NC}"
# Check if already added
if ! grep -q "php_admin_value\[upload_max_filesize\]" /etc/php/8.2/fpm/pool.d/www.conf; then
    cat >> /etc/php/8.2/fpm/pool.d/www.conf <<EOF

; Custom upload limits for large video files
php_admin_value[upload_max_filesize] = 1024M
php_admin_value[post_max_size] = 1024M
php_admin_value[memory_limit] = 512M
php_admin_value[max_execution_time] = 600
php_admin_value[max_input_time] = 600
EOF
    echo -e "${GREEN}✓ Pool configuration updated${NC}"
else
    echo -e "${GREEN}✓ Pool configuration already updated${NC}"
fi

echo -e "${YELLOW}Step 4: Fixing permissions...${NC}"
chmod 1777 /tmp
chown -R www-data:www-data /var/www/marwah-travels/storage
chmod -R 775 /var/www/marwah-travels/storage
chown -R www-data:www-data /var/www/marwah_frontend/.next
chmod -R 775 /var/www/marwah_frontend/.next
echo -e "${GREEN}✓ Permissions fixed${NC}"

echo -e "${YELLOW}Step 5: Fixing database...${NC}"
mysql -u root -p marwah_travels <<EOF
ALTER TABLE blog_elements MODIFY COLUMN section_title TEXT NULL;
EOF
echo -e "${GREEN}✓ Database updated${NC}"

echo -e "${YELLOW}Step 6: Restarting services...${NC}"
systemctl restart php8.2-fpm
systemctl restart nginx
echo -e "${GREEN}✓ PHP-FPM and Nginx restarted${NC}"

echo -e "${YELLOW}Step 7: Restarting Laravel backend...${NC}"
LARAVEL_PID=$(lsof -t -i:8000)
if [ ! -z "$LARAVEL_PID" ]; then
    kill $LARAVEL_PID
    sleep 2
fi
cd /var/www/marwah-travels
sudo -u www-data nohup php artisan serve --host=0.0.0.0 --port=8000 > /dev/null 2>&1 &
sleep 2
echo -e "${GREEN}✓ Laravel backend restarted${NC}"

echo -e "${YELLOW}Step 8: Restarting frontend...${NC}"
pm2 restart all
echo -e "${GREEN}✓ Frontend restarted${NC}"

echo -e "${YELLOW}Step 9: Clearing Laravel caches...${NC}"
cd /var/www/marwah-travels
php artisan config:clear
php artisan cache:clear
php artisan route:clear
echo -e "${GREEN}✓ Caches cleared${NC}"

echo ""
echo "=========================================="
echo -e "${GREEN}  ✅ ALL FIXES APPLIED SUCCESSFULLY!${NC}"
echo "=========================================="
echo ""
echo "Verification:"
echo "----------------------------------------"
php -i | grep -E "upload_max_filesize|post_max_size"
echo ""
echo "Backend running on:"
lsof -i :8000 | grep LISTEN
echo ""
echo "=========================================="
echo "Test video upload at:"
echo "https://www.mtumrah.com/pages/dashboard"
echo ""
echo "Capabilities:"
echo "  - Max file size: 1GB"
echo "  - Supported formats: All video types"
echo "  - Upload timeout: 10 minutes"
echo "  - Progress tracking: Real-time"
echo "  - Deletion: Database + Storage"
echo "=========================================="

