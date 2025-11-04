# Testimonials Video Upload - Complete Fix Documentation

## 🎯 Problem Summary

### Issues Reported:
1. ❌ Video uploads taking too long and timing out
2. ❌ Only MP4 format allowed, other formats rejected
3. ❌ Even small MP4 files (3-4MB) failing to upload
4. ❌ Large files (78MB+) getting 413 "Content Too Large" error
5. ❌ Delete only removing from frontend, not from database or storage

### Errors Encountered:
- **413 Content Too Large** - File too large for server
- **422 Unprocessable Content** - Validation failed: "The video failed to upload"

---

## 🔍 Root Cause Analysis

### The Real Problem:
Laravel backend was running via `php artisan serve` which uses **CLI PHP**, not **PHP-FPM**.

**Critical Discovery:**
- PHP-FPM settings: `/etc/php/8.2/fpm/php.ini` ← Updated to 1024M ✅
- CLI PHP settings: `/etc/php/8.2/cli/php.ini` ← Still had 2M limits! ❌

**Result:** Updates to PHP-FPM config had no effect because the backend wasn't using PHP-FPM.

---

## ✅ Complete Solution Applied

### 1. Backend Code Changes

**File: `app/Http/Controllers/ReviewController.php`**

Changed:
```php
// OLD: Limited formats and size
'video' => 'nullable|file|mimes:mp4,mov,avi|max:10240', // 10MB

// NEW: All formats, 1GB max
'video' => 'nullable|file|mimes:mp4,mov,avi,wmv,flv,mkv,webm,m4v,3gp,mpeg,mpg,ogv,ts,m2ts,mts|max:1048576', // 1GB
```

Added:
```php
// Increase timeout for large uploads
set_time_limit(600); // 10 minutes
```

Fixed deletion:
```php
// Now deletes BOTH database record AND file from storage
public function destroy($id)
{
    $review = Review::findOrFail($id);
    
    // Delete the video file from storage
    if ($review->video_url) {
        $videoPath = str_replace('/storage/', '', $review->video_url);
        if (Storage::disk('public')->exists($videoPath)) {
            Storage::disk('public')->delete($videoPath);
        }
    }
    
    $review->delete();
    return response()->json(['message' => 'Review and video deleted successfully'], 200);
}
```

**File: `routes/api.php`**

Added proper DELETE route:
```php
Route::delete('/{id}', [ReviewController::class, 'destroy']);
Route::post('/{id}', [ReviewController::class, 'update']); // For multipart updates
```

---

### 2. Frontend Code Changes

**File: `app/admin/addReviewDialog.tsx`**

Added:
- ✅ Real-time upload progress bar (0-100%)
- ✅ File size validation (1GB limit)
- ✅ File size display (human-readable: MB/GB)
- ✅ File type display
- ✅ Warning for large files (>500MB)
- ✅ XMLHttpRequest for progress tracking
- ✅ 10-minute timeout
- ✅ Proper error handling with toast notifications
- ✅ Form validation before upload
- ✅ Disabled form during upload
- ✅ Can't close dialog during upload

**File: `app/admin/tabs/ReviewsTab.tsx`**

Updated delete messages for clarity.

---

### 3. Server Configuration Changes

#### A. PHP-FPM Configuration (for when using PHP-FPM)

**File: `/etc/php/8.2/fpm/php.ini`**
```ini
upload_max_filesize = 1024M
post_max_size = 1024M
memory_limit = 512M
max_execution_time = 600
max_input_time = 600
```

**File: `/etc/php/8.2/fpm/pool.d/www.conf`**
```ini
php_admin_value[upload_max_filesize] = 1024M
php_admin_value[post_max_size] = 1024M
php_admin_value[memory_limit] = 512M
php_admin_value[max_execution_time] = 600
php_admin_value[max_input_time] = 600
```

#### B. CLI PHP Configuration (CRITICAL - This Fixed It!)

**File: `/etc/php/8.2/cli/php.ini`**
```ini
upload_max_filesize = 1024M
post_max_size = 1024M
memory_limit = 512M
max_execution_time = 600
max_input_time = 600
```

**Why This Matters:**
When running Laravel with `php artisan serve`, it uses CLI PHP settings, NOT PHP-FPM settings.

#### C. Nginx Configuration

**File: `/etc/nginx/sites-available/mtumrah`**

Already had:
```nginx
client_max_body_size 1024M;
```

In all server blocks and /api location. ✅

#### D. Permissions Fixed

```bash
# Upload temp directory:
sudo chmod 1777 /tmp

# Laravel storage:
sudo chown -R www-data:www-data /var/www/marwah-travels/storage
sudo chmod -R 775 /var/www/marwah-travels/storage

# Frontend cache:
sudo chown -R www-data:www-data /var/www/marwah_frontend/.next
sudo chmod -R 775 /var/www/marwah_frontend/.next
```

#### E. Database Fixes

```sql
-- Fix blog_elements table for long section titles:
ALTER TABLE `blog_elements` 
MODIFY COLUMN `section_title` TEXT NULL;

-- Fix hotels table (if needed):
ALTER TABLE `hotels` 
ADD COLUMN IF NOT EXISTS `currency` VARCHAR(255) NULL DEFAULT 'USD',
ADD COLUMN IF NOT EXISTS `phone` VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS `email` VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS `status` VARCHAR(255) NULL DEFAULT 'active',
ADD COLUMN IF NOT EXISTS `breakfast_enabled` TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS `dinner_enabled` TINYINT(1) DEFAULT 0;
```

---

## 🚀 Step-by-Step Deployment Guide

### Prerequisites
- SSH access to server
- Root/sudo privileges
- Backend code updated in Git repository
- Frontend code updated in Git repository

### Step 1: Update Server PHP Configuration

```bash
# SSH into server
ssh ubuntu@your-server

# Update PHP-FPM settings
sudo nano /etc/php/8.2/fpm/php.ini
# Set: upload_max_filesize=1024M, post_max_size=1024M, memory_limit=512M
# Set: max_execution_time=600, max_input_time=600

# Update CLI PHP settings (CRITICAL!)
sudo nano /etc/php/8.2/cli/php.ini
# Set: upload_max_filesize=1024M, post_max_size=1024M, memory_limit=512M
# Set: max_execution_time=600, max_input_time=600

# Add to pool config:
echo "
php_admin_value[upload_max_filesize] = 1024M
php_admin_value[post_max_size] = 1024M
php_admin_value[memory_limit] = 512M
php_admin_value[max_execution_time] = 600
php_admin_value[max_input_time] = 600" | sudo tee -a /etc/php/8.2/fpm/pool.d/www.conf

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

### Step 2: Fix Permissions

```bash
# Fix temp directory
sudo chmod 1777 /tmp

# Fix Laravel storage
cd /var/www/marwah-travels
sudo chown -R www-data:www-data storage
sudo chmod -R 775 storage

# Fix frontend cache
cd /var/www/marwah_frontend
sudo chown -R www-data:www-data .next
sudo chmod -R 775 .next
```

### Step 3: Fix Database

```bash
mysql -u root -p marwah_travels
```

```sql
-- Fix section_title length for blogs
ALTER TABLE `blog_elements` 
MODIFY COLUMN `section_title` TEXT NULL;

-- Exit
EXIT;
```

### Step 4: Deploy Code Changes

```bash
# Backend
cd /var/www/marwah-travels
git pull origin main  # or your branch
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Frontend
cd /var/www/marwah_frontend
git pull origin main  # or your branch
npm run build
pm2 restart all
```

### Step 5: Restart Laravel Backend

**Find current Laravel process:**
```bash
sudo lsof -i :8000
```

**Kill and restart:**
```bash
# Kill the old process (replace PID with actual):
sudo kill <PID>

# Start fresh:
cd /var/www/marwah-travels
sudo -u www-data nohup php artisan serve --host=0.0.0.0 --port=8000 > /dev/null 2>&1 &
```

### Step 6: Restart Nginx

```bash
sudo nginx -t
sudo systemctl restart nginx
```

---

## ✅ Verification Steps

### Test Upload Limits Are Applied

```bash
# Verify CLI PHP settings:
php -i | grep -E "upload_max_filesize|post_max_size|max_execution_time"
```

Should show:
```
max_execution_time => 600 => 600
post_max_size => 1024M => 1024M
upload_max_filesize => 1024M => 1024M
```

### Test Video Upload

1. Go to: https://www.mtumrah.com/pages/dashboard
2. Navigate to **Testimonials** tab
3. Click **Add Testimonial** button
4. Fill in user name and details
5. Select a video file (test with 4MB, then 50MB, then 500MB)
6. Watch the **upload progress bar** (0-100%)
7. Upload should complete successfully ✅

### Test Video Deletion

1. Upload a test video
2. Note the filename in `/var/www/marwah-travels/storage/app/public/videos/`
3. Click **Delete** button on the review card
4. Verify:
   - ✅ Removed from frontend display
   - ✅ Removed from database
   - ✅ Video file deleted from storage folder

```bash
# Check videos directory:
ls -la /var/www/marwah-travels/storage/app/public/videos/
```

---

## 📊 Current Capabilities

| Feature | Specification |
|---------|--------------|
| **Max Upload Size** | 1GB (1,048,576 KB) |
| **Supported Formats** | mp4, mov, avi, wmv, flv, mkv, webm, m4v, 3gp, mpeg, mpg, ogv, ts, m2ts, mts |
| **Upload Timeout** | 10 minutes (600 seconds) |
| **Progress Tracking** | Real-time percentage display (0-100%) |
| **File Size Display** | Human-readable (MB/GB) |
| **Large File Warning** | Shows warning for files >500MB |
| **Deletion** | Removes from database AND storage |
| **Update** | Auto-deletes old video when uploading new one |
| **Preview** | All video formats preview in admin dashboard |

---

## 🔍 Troubleshooting

### Issue: Still Getting 413 Error

**Check:**
```bash
# Verify Nginx is using the config:
sudo nginx -T 2>&1 | grep "client_max_body_size"

# Should show multiple lines with 1024M
```

**Fix:**
```bash
sudo systemctl restart nginx
```

### Issue: Still Getting 422 Error

**Check:**
```bash
# Verify CLI PHP settings:
php -i | grep upload_max_filesize

# Verify Laravel backend is using new PHP:
sudo lsof -i :8000  # Note the PID
ps aux | grep <PID>  # Check when it started
```

**Fix:**
```bash
# Kill old process and start fresh:
sudo kill <PID>
cd /var/www/marwah-travels
sudo -u www-data nohup php artisan serve --host=0.0.0.0 --port=8000 > /dev/null 2>&1 &
```

### Issue: Video Not Deleting from Storage

**Check:**
```bash
# Verify storage permissions:
ls -la /var/www/marwah-travels/storage/app/public/videos/

# Check if www-data can write:
sudo -u www-data touch /var/www/marwah-travels/storage/app/public/videos/test.txt
sudo -u www-data rm /var/www/marwah-travels/storage/app/public/videos/test.txt
```

**Fix:**
```bash
sudo chown -R www-data:www-data /var/www/marwah-travels/storage
sudo chmod -R 775 /var/www/marwah-travels/storage
```

### Issue: Upload Progress Not Showing

**Check:** Browser console for errors

**Fix:** Clear browser cache and reload page

### Issue: Disk Space Full

**Check:**
```bash
df -h
```

**Fix:**
```bash
# Clean up if root partition >90% full:
sudo apt-get clean
sudo apt-get autoremove
sudo journalctl --vacuum-size=100M

# Remove old log files:
sudo find /var/log -type f -name "*.log" -mtime +30 -delete
```

---

## 📝 Configuration Files Summary

### PHP Configuration Files

**1. PHP-FPM (if using PHP-FPM):**
- Main: `/etc/php/8.2/fpm/php.ini`
- Pool: `/etc/php/8.2/fpm/pool.d/www.conf`

**2. CLI PHP (if using artisan serve):** ⚠️ IMPORTANT
- File: `/etc/php/8.2/cli/php.ini`
- This is what `php artisan serve` uses!

**Settings to Update in ALL php.ini files:**
```ini
upload_max_filesize = 1024M
post_max_size = 1024M
memory_limit = 512M
max_execution_time = 600
max_input_time = 600
```

### Nginx Configuration

**File:** `/etc/nginx/sites-available/mtumrah`

**Settings:**
```nginx
server {
    # ... other settings ...
    
    client_max_body_size 1024M;
    
    # Buffer and timeout settings
    proxy_buffer_size 128k;
    proxy_buffers 4 256k;
    proxy_busy_buffers_size 256k;
    proxy_connect_timeout 300;
    proxy_send_timeout 300;
    proxy_read_timeout 300;
    send_timeout 300;
    
    location /api {
        client_max_body_size 1024M;  # ← Also in /api block
        proxy_pass http://127.0.0.1:8000;
        # ... other proxy settings ...
    }
}
```

---

## 🎬 Quick Deployment Script

Save this as `deploy_testimonials_fix.sh`:

```bash
#!/bin/bash

echo "=========================================="
echo "  Testimonials Video Upload Fix"
echo "=========================================="

# 1. Update CLI PHP settings
echo "Updating CLI PHP configuration..."
sudo sed -i 's/upload_max_filesize = .*/upload_max_filesize = 1024M/' /etc/php/8.2/cli/php.ini
sudo sed -i 's/post_max_size = .*/post_max_size = 1024M/' /etc/php/8.2/cli/php.ini
sudo sed -i 's/memory_limit = .*/memory_limit = 512M/' /etc/php/8.2/cli/php.ini
sudo sed -i 's/max_execution_time = .*/max_execution_time = 600/' /etc/php/8.2/cli/php.ini
sudo sed -i 's/max_input_time = .*/max_input_time = 600/' /etc/php/8.2/cli/php.ini

# 2. Update PHP-FPM settings
echo "Updating PHP-FPM configuration..."
sudo sed -i 's/upload_max_filesize = .*/upload_max_filesize = 1024M/' /etc/php/8.2/fpm/php.ini
sudo sed -i 's/post_max_size = .*/post_max_size = 1024M/' /etc/php/8.2/fpm/php.ini
sudo sed -i 's/memory_limit = .*/memory_limit = 512M/' /etc/php/8.2/fpm/php.ini
sudo sed -i 's/max_execution_time = .*/max_execution_time = 600/' /etc/php/8.2/fpm/php.ini
sudo sed -i 's/max_input_time = .*/max_input_time = 600/' /etc/php/8.2/fpm/php.ini

# 3. Fix permissions
echo "Fixing permissions..."
sudo chmod 1777 /tmp
sudo chown -R www-data:www-data /var/www/marwah-travels/storage
sudo chmod -R 775 /var/www/marwah-travels/storage
sudo chown -R www-data:www-data /var/www/marwah_frontend/.next
sudo chmod -R 775 /var/www/marwah_frontend/.next

# 4. Restart services
echo "Restarting services..."
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx

# 5. Restart Laravel backend
echo "Restarting Laravel backend..."
LARAVEL_PID=$(sudo lsof -t -i:8000)
if [ ! -z "$LARAVEL_PID" ]; then
    sudo kill $LARAVEL_PID
    sleep 2
fi
cd /var/www/marwah-travels
sudo -u www-data nohup php artisan serve --host=0.0.0.0 --port=8000 > /dev/null 2>&1 &

# 6. Restart frontend
echo "Restarting frontend..."
pm2 restart all

# 7. Clear Laravel caches
echo "Clearing Laravel caches..."
cd /var/www/marwah-travels
php artisan config:clear
php artisan cache:clear
php artisan route:clear

echo ""
echo "=========================================="
echo "  ✅ All Fixes Applied!"
echo "=========================================="
echo ""
echo "Verification:"
php -i | grep -E "upload_max_filesize|post_max_size"
echo ""
echo "Test video upload at:"
echo "https://www.mtumrah.com/pages/dashboard"
echo ""
```

**To use:**
```bash
chmod +x deploy_testimonials_fix.sh
sudo bash deploy_testimonials_fix.sh
```

---

## 📊 Files Modified

### Backend Files
1. `app/Http/Controllers/ReviewController.php` - Upload/delete logic
2. `routes/api.php` - Added DELETE and POST routes

### Frontend Files
1. `app/admin/addReviewDialog.tsx` - Upload progress bar & validation
2. `app/admin/tabs/ReviewsTab.tsx` - Delete messages

### Server Configuration Files
1. `/etc/php/8.2/cli/php.ini` - CLI PHP limits (CRITICAL!)
2. `/etc/php/8.2/fpm/php.ini` - PHP-FPM limits
3. `/etc/php/8.2/fpm/pool.d/www.conf` - Pool overrides
4. `/etc/nginx/sites-available/mtumrah` - Already configured

### Database Changes
1. `blog_elements` table - `section_title` changed to TEXT

---

## 🎯 Key Learnings

### Critical Discovery
**When using `php artisan serve`:**
- ❌ Does NOT use PHP-FPM settings
- ✅ Uses CLI PHP settings from `/etc/php/8.2/cli/php.ini`
- Must update BOTH cli and fpm php.ini files
- Must restart the artisan serve process after updating

### Why This Was Confusing
1. Updated `/etc/php/8.2/fpm/php.ini` → No effect
2. Added pool config overrides → No effect
3. Checked `php -i` → Showed 2M (CLI)
4. Checked `php-fpm8.2 -i` → Showed 1024M (FPM)
5. **Problem:** Laravel was using CLI, not FPM!

### The Fix
Update `/etc/php/8.2/cli/php.ini` and restart Laravel backend.

---

## ✅ Success Indicators

After applying all fixes, you should see:

1. **Small videos (3-5MB):**
   - Upload in seconds ✅
   - Progress bar shows 0-100% ✅
   - Success message appears ✅

2. **Medium videos (50-100MB):**
   - Upload in 30-60 seconds ✅
   - Progress bar updates smoothly ✅
   - No timeout errors ✅

3. **Large videos (500MB-1GB):**
   - Upload in 2-10 minutes ✅
   - Progress bar shows continuous progress ✅
   - Warning message for large file shown ✅
   - No 413 or 422 errors ✅

4. **Deletion:**
   - Review removed from frontend ✅
   - Record removed from database ✅
   - Video file deleted from `/var/www/marwah-travels/storage/app/public/videos/` ✅

5. **Storage location:**
   - Videos saved in: `/var/www/marwah-travels/storage/app/public/videos/`
   - Accessible via: `https://www.mtumrah.com/storage/videos/filename.mp4`

---

## 🆘 Emergency Rollback

If something goes wrong:

```bash
# Restore PHP settings:
sudo cp /etc/php/8.2/cli/php.ini.backup /etc/php/8.2/cli/php.ini
sudo cp /etc/php/8.2/fpm/php.ini.backup /etc/php/8.2/fpm/php.ini

# Restart services:
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx

# Restart Laravel:
sudo lsof -t -i:8000 | xargs sudo kill
cd /var/www/marwah-travels
sudo -u www-data php artisan serve --host=0.0.0.0 --port=8000 &
```

---

## 📞 Support Information

### Log Files to Check
```bash
# Laravel logs:
sudo tail -f /var/www/marwah-travels/storage/logs/laravel.log

# Nginx logs:
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log

# PHP-FPM logs:
sudo tail -f /var/log/php8.2-fpm.log

# Frontend logs:
pm2 logs
```

### Common Commands
```bash
# Check what's on port 8000:
sudo lsof -i :8000

# Check PHP settings:
php -i | grep -E "upload_max_filesize|post_max_size"

# Check disk space:
df -h

# Check Laravel backend status:
curl http://127.0.0.1:8000/api/reviews/test
```

---

## 🎉 Result

**Before:**
- ❌ Max upload: 10MB
- ❌ Formats: mp4, mov, avi only
- ❌ Upload timeout: 30 seconds
- ❌ No progress indicator
- ❌ Poor error handling
- ❌ Delete: Database only

**After:**
- ✅ Max upload: 1GB (1024MB)
- ✅ Formats: 14+ video types (all common formats)
- ✅ Upload timeout: 10 minutes
- ✅ Real-time progress bar (0-100%)
- ✅ File size validation and display
- ✅ Toast notifications for all states
- ✅ Delete: Database + Storage file
- ✅ Auto-cleanup old videos on update

**Status:** ✅ **FULLY OPERATIONAL**

---

## 📅 Date Resolved
**November 4, 2025**

## 👨‍💻 Resolution
Updated both CLI and FPM PHP configurations, fixed permissions, updated backend code for proper file deletion, and added comprehensive upload progress tracking in frontend.

---

**For future reference:** Always check which PHP binary your application is using (CLI vs FPM) and update the corresponding php.ini file!

