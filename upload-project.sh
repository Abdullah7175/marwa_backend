#!/bin/bash

# Marwah Travels - Project Upload Script
# This script helps upload your Laravel project to AWS EC2

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if required parameters are provided
if [ $# -lt 2 ]; then
    print_error "Usage: $0 <path-to-your-key.pem> <ec2-public-ip>"
    echo "Example: $0 ~/my-key.pem 54.123.45.67"
    exit 1
fi

KEY_PATH=$1
EC2_IP=$2

# Check if key file exists
if [ ! -f "$KEY_PATH" ]; then
    print_error "Key file not found: $KEY_PATH"
    exit 1
fi

print_status "🚀 Starting project upload to AWS EC2..."

# Set proper permissions for the key file
chmod 400 "$KEY_PATH"

# Create a temporary directory for upload
TEMP_DIR="/tmp/marwah-travels-upload"
mkdir -p "$TEMP_DIR"

print_status "📁 Preparing project files for upload..."

# Copy project files to temp directory (excluding unnecessary files)
rsync -av --progress \
    --exclude='node_modules/' \
    --exclude='vendor/' \
    --exclude='.git/' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='.env' \
    --exclude='database.sqlite' \
    --exclude='*.log' \
    ./ "$TEMP_DIR/"

print_status "📤 Uploading project files to EC2..."

# Upload files to EC2
scp -i "$KEY_PATH" -r "$TEMP_DIR"/* ubuntu@$EC2_IP:/home/ubuntu/marwah-travels-temp/

print_status "📋 Moving files to production directory..."

# Move files to production directory and set permissions
ssh -i "$KEY_PATH" ubuntu@$EC2_IP << 'EOF'
    sudo rm -rf /var/www/marwah-travels/*
    sudo cp -r /home/ubuntu/marwah-travels-temp/* /var/www/marwah-travels/
    sudo chown -R www-data:www-data /var/www/marwah-travels
    sudo chmod -R 755 /var/www/marwah-travels
    sudo chmod -R 775 /var/www/marwah-travels/storage
    sudo chmod -R 775 /var/www/marwah-travels/bootstrap/cache
    rm -rf /home/ubuntu/marwah-travels-temp
EOF

print_status "📦 Installing PHP dependencies..."

# Install Composer dependencies
ssh -i "$KEY_PATH" ubuntu@$EC2_IP << 'EOF'
    cd /var/www/marwah-travels
    sudo -u www-data composer install --optimize-autoloader --no-dev
EOF

print_status "🎨 Installing and building frontend assets..."

# Install Node.js dependencies and build assets
ssh -i "$KEY_PATH" ubuntu@$EC2_IP << 'EOF'
    cd /var/www/marwah-travels
    sudo -u www-data npm install
    sudo -u www-data npm run build
EOF

print_status "🗄️ Uploading database files..."

# Upload database files
scp -i "$KEY_PATH" database_setup.sql ubuntu@$EC2_IP:/home/ubuntu/
scp -i "$KEY_PATH" dummy_data.sql ubuntu@$EC2_IP:/home/ubuntu/

print_status "📊 Setting up database..."

# Import database
ssh -i "$KEY_PATH" ubuntu@$EC2_IP << 'EOF'
    mysql -u marwah_user -pMarwahTravels2024! marwah_travels < /home/ubuntu/database_setup.sql
    mysql -u marwah_user -pMarwahTravels2024! marwah_travels < /home/ubuntu/dummy_data.sql
    rm /home/ubuntu/database_setup.sql /home/ubuntu/dummy_data.sql
EOF

print_status "⚙️ Configuring Laravel environment..."

# Create .env file
ssh -i "$KEY_PATH" ubuntu@$EC2_IP << 'EOF'
    cd /var/www/marwah-travels
    sudo cp .env.example .env
    
    # Update .env file with production settings
    sudo sed -i 's/APP_ENV=local/APP_ENV=production/' .env
    sudo sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
    sudo sed -i 's/DB_DATABASE=laravel/DB_DATABASE=marwah_travels/' .env
    sudo sed -i 's/DB_USERNAME=root/DB_USERNAME=marwah_user/' .env
    sudo sed -i 's/DB_PASSWORD=/DB_PASSWORD=MarwahTravels2024!/' .env
    
    # Generate application key
    sudo -u www-data php artisan key:generate
    
    # Cache configuration
    sudo -u www-data php artisan config:cache
    sudo -u www-data php artisan route:cache
    sudo -u www-data php artisan view:cache
EOF

print_status "🔧 Setting up cron jobs..."

# Set up Laravel scheduler
ssh -i "$KEY_PATH" ubuntu@$EC2_IP << 'EOF'
    (crontab -l 2>/dev/null; echo "* * * * * cd /var/www/marwah-travels && php artisan schedule:run >> /dev/null 2>&1") | crontab -
EOF

# Clean up temporary directory
rm -rf "$TEMP_DIR"

print_status "✅ Project uploaded and configured successfully!"
print_warning "Important next steps:"
echo "1. Update your domain name in Nginx configuration:"
echo "   sudo nano /etc/nginx/sites-available/marwah-travels"
echo "   Replace 'server_name _;' with 'server_name your-domain.com;'"
echo ""
echo "2. Test your application:"
echo "   curl http://$EC2_IP"
echo ""
echo "3. Set up SSL certificate (recommended):"
echo "   sudo certbot --nginx -d your-domain.com"
echo ""
echo "4. Update your .env file with correct APP_URL:"
echo "   sudo nano /var/www/marwah-travels/.env"
echo "   Set APP_URL=https://your-domain.com"

print_status "🎉 Marwah Travels backend is now deployed and ready!"

