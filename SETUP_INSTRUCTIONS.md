# Marwah Travels - Complete Database Setup Guide

## Overview
This guide will help you recreate your Laravel database with all tables and dummy data based on your client images showing Umrah packages and testimonials.

## About Vite in Your Project
**Vite** is Laravel's modern frontend build tool that:
- Compiles CSS and JavaScript assets (`resources/css/app.css`, `resources/js/app.js`)
- Provides hot module replacement during development
- Optimizes assets for production
- Manages frontend dependencies

## Prerequisites
- MySQL server running
- Access to MySQL command line or phpMyAdmin
- Laravel project files (you already have these)

## Step 1: Create Database and Tables

### Option A: Using MySQL Command Line
```bash
# Connect to MySQL
mysql -u root -p

# Run the database setup script
source /path/to/your/project/database_setup.sql
```

### Option B: Using phpMyAdmin
1. Open phpMyAdmin
2. Create a new database named `marwah_travels`
3. Import the `database_setup.sql` file

### Option C: Using Laravel Artisan (Recommended)
```bash
# Navigate to your project directory
cd E:\SEO\Marwah_backend

# Create database manually first
mysql -u root -p -e "CREATE DATABASE marwah_travels CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Update your .env file with database credentials
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=marwah_travels
# DB_USERNAME=root
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed
```

## Step 2: Insert Dummy Data

### Option A: Using MySQL Command Line
```bash
# Connect to MySQL
mysql -u root -p

# Run the dummy data script
source /path/to/your/project/dummy_data.sql
```

### Option B: Using phpMyAdmin
1. Select the `marwah_travels` database
2. Import the `dummy_data.sql` file

### Option C: Using Laravel Artisan
```bash
# Run the seeder
php artisan db:seed --class=SeoSeeder

# Or run all seeders
php artisan db:seed
```

## Step 3: Verify Database Setup

### Check Tables Created
```sql
USE marwah_travels;
SHOW TABLES;
```

Expected tables:
- users
- categories
- inquiries
- blogs
- blog_elements
- hotels
- packages
- custom_packages
- reviews
- seo_settings
- migrations
- cache, cache_locks
- jobs, job_batches, failed_jobs
- personal_access_tokens
- password_reset_tokens
- sessions

### Check Data Inserted
```sql
-- Check packages
SELECT name, price_single, hotel_makkah_name, hotel_madina_name FROM packages;

-- Check reviews/testimonials
SELECT user_name, detail FROM reviews;

-- Check categories
SELECT name, status FROM categories;

-- Check hotels
SELECT name, location, rating FROM hotels;
```

## Step 4: Frontend Asset Compilation (Vite)

### Install Dependencies
```bash
npm install
```

### Development Mode
```bash
npm run dev
```

### Production Build
```bash
npm run build
```

## Step 5: Laravel Configuration

### Update .env File
```env
APP_NAME="Marwah Travels"
APP_ENV=local
APP_KEY=base64:your_app_key_here
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marwah_travels
DB_USERNAME=root
DB_PASSWORD=your_password

# Generate app key if needed
php artisan key:generate
```

### Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

## Step 6: Test Your Application

### Start Laravel Server
```bash
php artisan serve
```

### Access Your Application
- Frontend: http://localhost:8000
- Check if packages are displaying correctly
- Verify testimonials are showing
- Test contact forms

## Database Structure Summary

### Core Tables
1. **packages** - Umrah packages with pricing, hotels, inclusions
2. **reviews** - Customer testimonials with video URLs
3. **hotels** - Hotel information for packages
4. **categories** - Package categories (Umrah, Hajj, etc.)
5. **blogs** - Blog posts with SEO fields
6. **inquiries** - Contact form submissions
7. **seo_settings** - SEO metadata for pages

### Features Included
- ✅ Umrah packages with luxury accommodations
- ✅ Customer testimonials with video support
- ✅ Hotel information and ratings
- ✅ SEO optimization fields
- ✅ Blog system with rich content
- ✅ Contact inquiry system
- ✅ Custom package requests

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   ```bash
   # Check MySQL service
   sudo service mysql start
   
   # Verify credentials in .env
   php artisan config:clear
   ```

2. **Migration Errors**
   ```bash
   # Reset migrations
   php artisan migrate:reset
   php artisan migrate
   ```

3. **Asset Compilation Issues**
   ```bash
   # Clear node modules and reinstall
   rm -rf node_modules package-lock.json
   npm install
   npm run dev
   ```

4. **Permission Issues**
   ```bash
   # Set proper permissions
   chmod -R 755 storage bootstrap/cache
   ```

## Next Steps

1. **Customize Content**: Update package details, prices, and descriptions
2. **Add Images**: Upload hotel images, package images, and testimonial videos
3. **Configure SEO**: Update meta titles and descriptions for better search ranking
4. **Test Features**: Verify all forms, packages, and testimonials work correctly
5. **Deploy**: Prepare for production deployment

## Support

If you encounter any issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify database connection
3. Ensure all dependencies are installed
4. Check file permissions

Your Marwah Travels database is now ready with all the Umrah packages and testimonials from your client images!
