# AWS Laravel Deployment Guide - Marwah Travels Backend

## Prerequisites
- AWS EC2 instance running (Ubuntu 20.04/22.04 recommended)
- SSH access to your EC2 instance
- Domain name (optional but recommended)

## Step 1: Connect to Your AWS Instance

```bash
# Connect via SSH (replace with your key and IP)
ssh -i "your-key.pem" ubuntu@your-ec2-public-ip

# Update system packages
sudo apt update && sudo apt upgrade -y
```

## Step 2: Install Required Software

### Install PHP 8.2 and Extensions
```bash
# Add PHP repository
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.2 and required extensions
sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml php8.2-gd php8.2-curl php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-intl php8.2-readline php8.2-soap php8.2-sqlite3 php8.2-xmlrpc php8.2-xsl php8.2-opcache -y

# Verify PHP installation
php -v
```

### Install Composer
```bash
# Download and install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Verify Composer installation
composer --version
```

### Install Node.js and NPM
```bash
# Install Node.js 18.x
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Verify installation
node -v
npm -v
```

## Step 3: Install and Configure MySQL

### Install MySQL Server
```bash
# Install MySQL Server
sudo apt install mysql-server -y

# Secure MySQL installation
sudo mysql_secure_installation

# Start and enable MySQL
sudo systemctl start mysql
sudo systemctl enable mysql
sudo systemctl status mysql
```

### Configure MySQL for Laravel
```bash
# Login to MySQL as root
sudo mysql -u root -p

# Create database and user
CREATE DATABASE marwah_travels CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'marwah'@'localhost' IDENTIFIED BY '**@/#Abc1';
GRANT ALL PRIVILEGES ON marwah_travels.* TO 'marwah'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## Step 4: Install and Configure Nginx

### Install Nginx
```bash
# Install Nginx
sudo apt install nginx -y

# Start and enable Nginx
sudo systemctl start nginx
sudo systemctl enable nginx
sudo systemctl status nginx
```

### Configure Nginx for Laravel
```bash
# Create Nginx configuration for your site
sudo nano /etc/nginx/sites-available/marwah-travels
```

Add the following configuration:
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;  # Replace with your domain
    root /var/www/marwah-travels/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Enable the Site
```bash
# Enable the site
sudo ln -s /etc/nginx/sites-available/marwah-travels /etc/nginx/sites-enabled/

# Remove default site
sudo rm /etc/nginx/sites-enabled/default

# Test Nginx configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

## Step 5: Deploy Your Laravel Application

### Create Application Directory
```bash
# Create application directory
sudo mkdir -p /var/www/marwah-travels
sudo chown -R www-data:www-data /var/www/marwah-travels
sudo chmod -R 755 /var/www/marwah-travels
```

### Upload Your Project Files
```bash
# Option 1: Using SCP from your local machine
scp -i "your-key.pem" -r E:\SEO\Marwah_backend/* ubuntu@your-ec2-public-ip:/home/ubuntu/marwah-travels/

# Option 2: Using Git (recommended)
cd /var/www/marwah-travels
sudo git clone https://github.com/your-username/marwah-travels.git .
sudo chown -R www-data:www-data /var/www/marwah-travels
```

### Install Dependencies
```bash
# Navigate to project directory
cd /var/www/marwah-travels

# Install PHP dependencies
sudo -u www-data composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
sudo -u www-data npm install

# Build frontend assets
sudo -u www-data npm run build
```

## Step 6: Configure Laravel Environment

### Create Environment File
```bash
# Copy environment file
sudo cp .env.example .env

# Edit environment file
sudo nano .env
```

Update the following values in `.env`:
```env
APP_NAME="Marwah Travels"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marwah_travels
DB_USERNAME=marwah_user
DB_PASSWORD=your_strong_password_here

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Generate Application Key and Configure
```bash
# Generate application key
sudo -u www-data php artisan key:generate

# Set proper permissions
sudo chown -R www-data:www-data /var/www/marwah-travels
sudo chmod -R 755 /var/www/marwah-travels
sudo chmod -R 775 /var/www/marwah-travels/storage
sudo chmod -R 775 /var/www/marwah-travels/bootstrap/cache
```

## Step 7: Set Up Database

### Import Database Structure and Data
```bash
# Upload your SQL files to the server
scp -i "your-key.pem" database_setup.sql ubuntu@your-ec2-public-ip:/home/ubuntu/
scp -i "your-key.pem" dummy_data.sql ubuntu@your-ec2-public-ip:/home/ubuntu/

# Import database structure
mysql -u marwah_user -p marwah_travels < /home/ubuntu/database_setup.sql

# Import dummy data
mysql -u marwah_user -p marwah_travels < /home/ubuntu/dummy_data.sql
```

### Run Laravel Migrations (Alternative)
```bash
# Run migrations
sudo -u www-data php artisan migrate --force

# Run seeders
sudo -u www-data php artisan db:seed --force
```

## Step 8: Configure PHP-FPM

### Optimize PHP-FPM
```bash
# Edit PHP-FPM configuration
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
```

Update the following values:
```ini
user = www-data
group = www-data
listen = /var/run/php/php8.2-fpm.sock
listen.owner = www-data
listen.group = www-data
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
```

### Restart PHP-FPM
```bash
sudo systemctl restart php8.2-fpm
sudo systemctl status php8.2-fpm
```

## Step 9: Configure SSL with Let's Encrypt (Optional but Recommended)

### Install Certbot
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y
```

### Obtain SSL Certificate
```bash
# Get SSL certificate (replace with your domain)
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Test automatic renewal
sudo certbot renew --dry-run
```

## Step 10: Set Up Cron Jobs

### Configure Laravel Scheduler
```bash
# Edit crontab
sudo crontab -e

# Add Laravel scheduler
* * * * * cd /var/www/marwah-travels && php artisan schedule:run >> /dev/null 2>&1
```

## Step 11: Configure Firewall

### Set Up UFW Firewall
```bash
# Enable UFW
sudo ufw enable

# Allow SSH
sudo ufw allow ssh

# Allow HTTP and HTTPS
sudo ufw allow 'Nginx Full'

# Check status
sudo ufw status
```

## Step 12: Optimize Laravel for Production

### Clear and Cache
```bash
# Navigate to project directory
cd /var/www/marwah-travels

# Clear all caches
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan event:cache

# Optimize autoloader
sudo -u www-data composer install --optimize-autoloader --no-dev
```

## Step 13: Test Your Deployment

### Check Services Status
```bash
# Check all services
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql
```

### Test Your Application
```bash
# Test locally
curl http://localhost

# Test from external
curl http://your-domain.com
```

## Step 14: Set Up Monitoring and Logs

### Configure Log Rotation
```bash
# Edit logrotate configuration
sudo nano /etc/logrotate.d/laravel

# Add the following content
/var/www/marwah-travels/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 644 www-data www-data
}
```

### Set Up Log Monitoring
```bash
# Install htop for monitoring
sudo apt install htop -y

# Monitor logs
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/www/marwah-travels/storage/logs/laravel.log
```

## Step 15: Backup Strategy

### Create Database Backup Script
```bash
# Create backup script
sudo nano /home/ubuntu/backup-db.sh
```

Add the following content:
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/ubuntu/backups"
DB_NAME="marwah_travels"
DB_USER="marwah_user"
DB_PASS="your_strong_password_here"

mkdir -p $BACKUP_DIR
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/marwah_travels_$DATE.sql
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
```

### Make Script Executable
```bash
chmod +x /home/ubuntu/backup-db.sh

# Add to crontab for daily backups
sudo crontab -e
# Add: 0 2 * * * /home/ubuntu/backup-db.sh
```

## Troubleshooting

### Common Issues and Solutions

1. **Permission Issues**
```bash
sudo chown -R www-data:www-data /var/www/marwah-travels
sudo chmod -R 755 /var/www/marwah-travels
sudo chmod -R 775 /var/www/marwah-travels/storage
sudo chmod -R 775 /var/www/marwah-travels/bootstrap/cache
```

2. **Database Connection Issues**
```bash
# Check MySQL status
sudo systemctl status mysql

# Test database connection
mysql -u marwah_user -p marwah_travels -e "SELECT 1;"
```

3. **Nginx Configuration Issues**
```bash
# Test Nginx configuration
sudo nginx -t

# Check Nginx error logs
sudo tail -f /var/log/nginx/error.log
```

4. **PHP-FPM Issues**
```bash
# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Check PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log
```

## Security Checklist

- ✅ Firewall configured (UFW)
- ✅ SSL certificate installed
- ✅ Database user with limited privileges
- ✅ Laravel in production mode (APP_DEBUG=false)
- ✅ Proper file permissions set
- ✅ Regular backups configured
- ✅ Log monitoring set up

## Performance Optimization

- ✅ PHP-FPM optimized
- ✅ Laravel caches enabled
- ✅ Composer autoloader optimized
- ✅ Frontend assets built for production
- ✅ Database indexes created

Your Marwah Travels backend is now deployed and ready for production use!

