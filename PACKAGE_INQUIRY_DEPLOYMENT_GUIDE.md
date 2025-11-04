# Package Inquiry Feature - Deployment Guide

## 🎯 Feature Overview

**What's New:**
1. ✅ Smaller package image on package details page
2. ✅ Inquiry form displayed parallel to package image
3. ✅ Inquiry API enhanced with package details (19 new optional fields)
4. ✅ Webhook to booking portal includes package information
5. ✅ **Fully backward compatible** - existing inquiries still work

---

## 📋 Changes Summary

### Frontend Changes
- **Modified:** `app/pages/package_detail/page.tsx` - New layout with smaller image + inquiry form
- **Created:** `components/PackageInquiryForm.tsx` - New inquiry form component

### Backend Changes
- **Modified:** `app/Models/Inquiry.php` - Added package detail fields to fillable
- **Modified:** `app/Http/Controllers/WebController.php` - Enhanced validation and webhook payload
- **Created:** `database/migrations/2025_11_04_000000_add_package_details_to_inquiries_table.php`
- **Created:** `add_package_details_to_inquiries.sql` - Direct SQL migration

### Documentation Created
- **Created:** `BOOKING_PORTAL_API_DOCUMENTATION.md` - Complete API docs for booking portal developers
- **Created:** `PACKAGE_INQUIRY_DEPLOYMENT_GUIDE.md` - This file

---

## 🚀 Deployment Steps

### Step 1: Backup Database

```bash
# SSH into server
ssh ubuntu@your-server

# Create backup
mysqldump -u root -p marwah_travels > backup_inquiries_$(date +%Y%m%d_%H%M%S).sql
```

### Step 2: Update Database

**Option A: Using SQL file**
```bash
cd /var/www/marwah-travels
mysql -u root -p marwah_travels < add_package_details_to_inquiries.sql
```

**Option B: Using Laravel migration**
```bash
cd /var/www/marwah-travels
php artisan migrate
```

**Option C: Manual SQL**
```bash
mysql -u root -p marwah_travels
```

```sql
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

DESCRIBE inquiries;
EXIT;
```

### Step 3: Deploy Backend Code

```bash
cd /var/www/marwah-travels
git pull origin main

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Restart Laravel backend
sudo kill $(sudo lsof -t -i:8000)
sudo -u www-data nohup php artisan serve --host=0.0.0.0 --port=8000 > /dev/null 2>&1 &
```

### Step 4: Deploy Frontend Code

```bash
cd /var/www/marwah_frontend
git pull origin main

# Build frontend
npm run build

# Restart
pm2 restart all
```

### Step 5: Restart Services

```bash
sudo systemctl restart nginx
```

---

## ✅ Verification Steps

### Test 1: General Inquiry (from Home Page)

1. Go to: https://www.mtumrah.com/
2. Fill out inquiry form
3. Submit
4. **Verify:** Webhook received with only base fields (no package_details)

**Expected webhook payload:**
```json
{
  "id": 123,
  "name": "Test User",
  "email": "test@example.com",
  "phone": "1234567890",
  "message": "Test message",
  "created_at": "2025-11-04T..."
}
```

### Test 2: Package-Specific Inquiry (from Package Details Page)

1. Go to: https://www.mtumrah.com/pages/packages
2. Click on any package
3. On package details page, you should see:
   - ✅ Smaller package image (left side)
   - ✅ Inquiry form (right side)
4. Fill out inquiry form
5. Submit
6. **Verify:** Webhook received with package_details object

**Expected webhook payload:**
```json
{
  "id": 124,
  "name": "Test User 2",
  "email": "test2@example.com",
  "phone": "0987654321",
  "message": "Interested in this package",
  "created_at": "2025-11-04T...",
  "package_details": {
    "package_name": "Premium Umrah Package...",
    "pricing": { ... },
    "duration": { ... },
    "hotels": { ... },
    "services": { ... },
    "inclusions": { ... }
  }
}
```

### Test 3: Database Verification

```bash
mysql -u root -p marwah_travels
```

```sql
-- Check new columns exist
DESCRIBE inquiries;

-- Check data is being saved
SELECT id, name, email, package_name, price_double 
FROM inquiries 
ORDER BY id DESC 
LIMIT 5;

EXIT;
```

### Test 4: Visual Verification

1. Visit: https://www.mtumrah.com/pages/package_detail
2. **Check layout:**
   - ✅ Image is smaller (not full width)
   - ✅ Inquiry form appears next to it
   - ✅ On mobile, they stack vertically
   - ✅ Form has package name in description
   - ✅ Form submits successfully

---

## 🔧 Troubleshooting

### Issue: Form Not Showing

**Check:**
```bash
# Verify component exists:
ls -la /var/www/marwah_frontend/components/PackageInquiryForm.tsx

# Check build logs:
pm2 logs marwah-frontend
```

**Fix:**
```bash
cd /var/www/marwah_frontend
npm run build
pm2 restart all
```

### Issue: Database Migration Fails

**Error:** "Duplicate column name 'package_name'"

**Cause:** Columns already exist

**Fix:** Columns are already added, skip migration

### Issue: Webhook Not Sending Package Details

**Check:**
```bash
# Watch Laravel logs while submitting:
sudo tail -f /var/www/marwah-travels/storage/logs/laravel.log
```

**Verify:** Check database to see if package fields are populated

### Issue: Layout Broken on Mobile

**Check:** Browser console for errors

**Fix:** Clear browser cache and reload

---

## 📊 New Database Columns

| Column Name | Type | Nullable | Default | Description |
|-------------|------|----------|---------|-------------|
| `package_name` | VARCHAR(255) | YES | NULL | Name of the package |
| `price_double` | VARCHAR(255) | YES | NULL | Double occupancy price |
| `price_triple` | VARCHAR(255) | YES | NULL | Triple occupancy price |
| `price_quad` | VARCHAR(255) | YES | NULL | Quad occupancy price |
| `currency` | VARCHAR(255) | YES | NULL | Currency code |
| `nights_makkah` | VARCHAR(255) | YES | NULL | Nights in Makkah |
| `nights_madina` | VARCHAR(255) | YES | NULL | Nights in Madinah |
| `total_nights` | VARCHAR(255) | YES | NULL | Total nights |
| `hotel_makkah_name` | VARCHAR(255) | YES | NULL | Makkah hotel name |
| `hotel_madina_name` | VARCHAR(255) | YES | NULL | Madinah hotel name |
| `transportation_title` | VARCHAR(255) | YES | NULL | Transportation service |
| `visa_title` | VARCHAR(255) | YES | NULL | Visa service |
| `breakfast_included` | TINYINT(1) | YES | NULL | Breakfast flag |
| `dinner_included` | TINYINT(1) | YES | NULL | Dinner flag |
| `visa_included` | TINYINT(1) | YES | NULL | Visa flag |
| `ticket_included` | TINYINT(1) | YES | NULL | Ticket flag |
| `roundtrip` | TINYINT(1) | YES | NULL | Roundtrip flag |
| `ziyarat_included` | TINYINT(1) | YES | NULL | Ziyarat flag |
| `guide_included` | TINYINT(1) | YES | NULL | Guide flag |

**Total:** 19 new columns (all nullable, all optional)

---

## 📞 Booking Portal Developers

### Share This Document

Send `BOOKING_PORTAL_API_DOCUMENTATION.md` to your booking portal development team.

**It includes:**
- Complete webhook payload structure
- Signature verification examples (Python, Node.js, PHP)
- Sample implementations
- Test data
- Integration checklist
- Backward compatibility notes

### Configuration Needed

Booking portal team needs to provide:
1. **Webhook URL** - Their endpoint to receive inquiries
2. **Webhook Secret** - Shared secret for HMAC signatures

You will add these to `/var/www/marwah-travels/.env`:
```env
INQUIRY_WEBHOOK_URL=https://booking-portal.com/api/webhooks/inquiries
INQUIRY_WEBHOOK_SECRET=shared_secret_key_from_booking_portal
```

---

## 📸 Visual Changes

### Before:
```
┌─────────────────────────────────────┐
│  [===== LARGE IMAGE  =====]         │
│  [===== FULL WIDTH   =====]         │
│                                      │
│  Package Features                   │
│                                      │
│  Package Details...                 │
└─────────────────────────────────────┘
```

### After:
```
┌────────────────────┬────────────────┐
│  [=== IMAGE ===]   │  ╔══════════╗  │
│  (Smaller)         │  ║ INQUIRY  ║  │
│                    │  ║  FORM    ║  │
│  Package Features  │  ║          ║  │
│                    │  ╚══════════╝  │
├────────────────────┴────────────────┤
│  Package Details Below...            │
└─────────────────────────────────────┘
```

---

## 🎯 Benefits

### For Sales Team:
- ✅ Know exactly which package customer is interested in
- ✅ See pricing customer was viewing
- ✅ Understand what's included in the package
- ✅ Pre-qualify leads better
- ✅ Faster quote generation

### For Customers:
- ✅ Quick inquiry form on package page
- ✅ Don't need to remember package details
- ✅ Faster response from sales team
- ✅ More accurate quotes

### For Booking Portal:
- ✅ Rich inquiry data
- ✅ Auto-populate booking forms
- ✅ Better lead scoring
- ✅ Improved conversion tracking

---

## 📝 Testing Checklist

After deployment:

- [ ] Database migration successful
- [ ] Backend code deployed
- [ ] Frontend code deployed and built
- [ ] Package details page shows new layout
- [ ] Image is smaller (not full width)
- [ ] Inquiry form appears next to image
- [ ] Form submission works
- [ ] Database stores package details
- [ ] Webhook includes package_details object
- [ ] Booking portal receives webhook successfully
- [ ] General inquiries (from home page) still work
- [ ] Mobile layout works (stacks vertically)

---

## 🔐 Security Notes

1. **Webhook Secret:** Keep it secure, rotate periodically
2. **Signature Verification:** Always verify signatures on booking portal side
3. **Timestamp Validation:** Reject old requests (>5 minutes)
4. **Idempotency:** Use Idempotency-Key to prevent duplicate processing
5. **HTTPS Only:** Webhook URL must use HTTPS

---

## 📊 Expected Impact

### Traffic Split (Estimated):
- General inquiries: 60-70%
- Package-specific inquiries: 30-40%

### Conversion Rate Improvement (Projected):
- General inquiries: 15-20% conversion
- Package inquiries: 30-40% conversion (higher intent!)

---

## 🆘 Rollback Plan

If issues arise:

### Rollback Database
```bash
# Restore from backup:
mysql -u root -p marwah_travels < backup_inquiries_YYYYMMDD_HHMMSS.sql
```

### Rollback Code
```bash
# Backend
cd /var/www/marwah-travels
git checkout HEAD~1  # Go back one commit
php artisan config:clear
php artisan cache:clear
sudo kill $(sudo lsof -t -i:8000)
sudo -u www-data nohup php artisan serve --host=0.0.0.0 --port=8000 > /dev/null 2>&1 &

# Frontend
cd /var/www/marwah_frontend
git checkout HEAD~1
npm run build
pm2 restart all
```

---

## 📞 Support

### Log Files

```bash
# Laravel logs
sudo tail -f /var/www/marwah-travels/storage/logs/laravel.log

# Frontend logs
pm2 logs marwah-frontend

# Nginx logs
sudo tail -f /var/log/nginx/error.log
```

### Webhook Testing

Test webhook manually:
```bash
curl -X POST https://www.mtumrah.com/api/inquiries/123/forward-webhook \
  -H "X-Api-Key: your-admin-api-key" \
  -H "Content-Type: application/json"
```

---

## ✅ Success Criteria

Feature is successful when:

1. ✅ Package details page loads with new layout
2. ✅ Image is smaller and looks good
3. ✅ Inquiry form appears beside image
4. ✅ Form submission includes package details
5. ✅ Webhook delivers to booking portal
6. ✅ Booking portal receives package information
7. ✅ Sales team can see package details in their system
8. ✅ General inquiries (home page) still work normally

---

## 🎉 Deployment Complete!

After running all steps above, the feature is live and ready for use.

**Next Steps:**
1. Share `BOOKING_PORTAL_API_DOCUMENTATION.md` with booking portal team
2. Configure webhook URL and secret in `.env`
3. Test end-to-end with booking portal
4. Monitor for first week
5. Gather feedback from sales team

---

**Date:** November 4, 2025  
**Status:** Ready for Deployment

