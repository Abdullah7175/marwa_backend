#!/bin/bash

# Marwah Travels - AWS Deployment Script
# This script automates the deployment process

set -e  # Exit on any error

echo "🚀 Starting Marwah Travels AWS Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    print_error "Please do not run this script as root. Use a regular user with sudo privileges."
    exit 1
fi

# Update system packages
print_status "Updating system packages..."
sudo apt update && sudo apt upgrade -y

# Install required packages
print_status "Installing required packages..."
sudo apt install software-properties-common curl wget unzip git -y

# Add PHP repository and install PHP 8.2
print_status "Installing PHP 8.2 and extensions..."
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml php8.2-gd php8.2-curl php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-intl php8.2-readline php8.2-soap php8.2-sqlite3 php8.2-xmlrpc php8.2-xsl php8.2-opcache -y

# Install Composer
print_status "Installing Composer..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Install Node.js and NPM
print_status "Installing Node.js and NPM..."
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Install MySQL Server
print_status "Installing MySQL Server..."
sudo apt install mysql-server -y

# Install Nginx
print_status "Installing Nginx..."
sudo apt install nginx -y

# Start and enable services
print_status "Starting and enabling services..."
sudo systemctl start mysql
sudo systemctl enable mysql
sudo systemctl start nginx
sudo systemctl enable nginx
sudo systemctl start php8.2-fpm
sudo systemctl enable php8.2-fpm

# Configure MySQL
print_status "Configuring MySQL..."
sudo mysql -e "CREATE DATABASE IF NOT EXISTS marwah_travels CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'marwah_user'@'localhost' IDENTIFIED BY 'MarwahTravels2024!';"
sudo mysql -e "GRANT ALL PRIVILEGES ON marwah_travels.* TO 'marwah_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Create application directory
print_status "Creating application directory..."
sudo mkdir -p /var/www/marwah-travels
sudo chown -R www-data:www-data /var/www/marwah-travels
sudo chmod -R 755 /var/www/marwah-travels

# Create Nginx configuration
print_status "Creating Nginx configuration..."
sudo tee /etc/nginx/sites-available/marwah-travels > /dev/null <<EOF
server {
    listen 80;
    server_name _;
    root /var/www/marwah-travels/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Enable the site
sudo ln -sf /etc/nginx/sites-available/marwah-travels /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

# Test Nginx configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx

# Configure PHP-FPM
print_status "Configuring PHP-FPM..."
sudo sed -i 's/user = www-data/user = www-data/' /etc/php/8.2/fpm/pool.d/www.conf
sudo sed -i 's/group = www-data/group = www-data/' /etc/php/8.2/fpm/pool.d/www.conf
sudo sed -i 's/listen = \/run\/php\/php8.2-fpm.sock/listen = \/var\/run\/php\/php8.2-fpm.sock/' /etc/php/8.2/fpm/pool.d/www.conf
sudo sed -i 's/listen.owner = www-data/listen.owner = www-data/' /etc/php/8.2/fpm/pool.d/www.conf
sudo sed -i 's/listen.group = www-data/listen.group = www-data/' /etc/php/8.2/fpm/pool.d/www.conf

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# Configure firewall
print_status "Configuring firewall..."
sudo ufw --force enable
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'

# Create backup script
print_status "Creating backup script..."
sudo tee /home/ubuntu/backup-db.sh > /dev/null <<EOF
#!/bin/bash
DATE=\$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/ubuntu/backups"
DB_NAME="marwah_travels"
DB_USER="marwah_user"
DB_PASS="MarwahTravels2024!"

mkdir -p \$BACKUP_DIR
mysqldump -u\$DB_USER -p\$DB_PASS \$DB_NAME > \$BACKUP_DIR/marwah_travels_\$DATE.sql
find \$BACKUP_DIR -name "*.sql" -mtime +7 -delete
EOF

sudo chmod +x /home/ubuntu/backup-db.sh

# Create logrotate configuration
print_status "Creating log rotation configuration..."
sudo tee /etc/logrotate.d/laravel > /dev/null <<EOF
/var/www/marwah-travels/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 644 www-data www-data
}
EOF

print_status "✅ Server setup completed successfully!"
print_warning "Next steps:"
echo "1. Upload your Laravel project files to /var/www/marwah-travels/"
echo "2. Run: sudo -u www-data composer install --optimize-autoloader --no-dev"
echo "3. Run: sudo -u www-data npm install && sudo -u www-data npm run build"
echo "4. Configure your .env file with database credentials"
echo "5. Import your database using the SQL files"
echo "6. Set proper permissions: sudo chown -R www-data:www-data /var/www/marwah-travels"
echo "7. Configure your domain name in Nginx configuration"

print_status "🎉 Marwah Travels server is ready for deployment!"

