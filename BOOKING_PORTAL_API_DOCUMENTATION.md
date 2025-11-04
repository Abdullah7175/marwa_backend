# Inquiry Webhook API - Documentation for Booking Portal Developers

**Version:** 2.0 (Enhanced with Package Details)  
**Date:** November 4, 2025  
**Status:** Production Ready

---

## 📝 Overview

This webhook delivers customer inquiries from the Marwah Travels website to your booking portal in real-time. The webhook now includes **optional package details** when customers inquire about specific packages from the package details page.

---

## 🔗 Webhook Endpoint Configuration

### Your Endpoint Setup

Your booking portal must provide:
1. **Webhook URL** - The endpoint to receive inquiry data
2. **Webhook Secret** - Shared secret for signature verification

These will be configured in our Laravel `.env` file:
```env
INQUIRY_WEBHOOK_URL=https://your-booking-portal.com/api/webhooks/inquiries
INQUIRY_WEBHOOK_SECRET=your_secure_secret_key_here
```

### HTTP Method
```
POST
```

### Content-Type
```
application/json
```

---

## 🔐 Security & Authentication

### Headers We Send

Every webhook request includes these security headers:

| Header | Description | Example |
|--------|-------------|---------|
| `Content-Type` | Always `application/json` | `application/json` |
| `X-Webhook-Timestamp` | Unix timestamp when request sent | `1730751234` |
| `X-Webhook-Signature` | HMAC-SHA256 signature | `abc123def456...` |
| `Idempotency-Key` | Unique key to prevent duplicates | `inq-123` |

### Signature Verification

**Algorithm:** HMAC-SHA256

**Signature Calculation:**
```python
# Python example
import hmac
import hashlib
import json

def verify_signature(timestamp, body_json, signature, secret):
    message = timestamp + '.' + body_json
    expected_signature = hmac.new(
        secret.encode('utf-8'),
        message.encode('utf-8'),
        hashlib.sha256
    ).hexdigest()
    return hmac.compare_digest(expected_signature, signature)
```

**Node.js example:**
```javascript
const crypto = require('crypto');

function verifySignature(timestamp, bodyJson, signature, secret) {
    const message = `${timestamp}.${bodyJson}`;
    const expectedSignature = crypto
        .createHmac('sha256', secret)
        .update(message)
        .digest('hex');
    return crypto.timingSafeEqual(
        Buffer.from(signature),
        Buffer.from(expectedSignature)
    );
}
```

**PHP example:**
```php
function verifySignature($timestamp, $bodyJson, $signature, $secret) {
    $message = $timestamp . '.' . $bodyJson;
    $expectedSignature = hash_hmac('sha256', $message, $secret);
    return hash_equals($expectedSignature, $signature);
}
```

---

## 📦 Webhook Payload Structure

### Two Types of Inquiries

#### Type 1: General Inquiry (from Home Page)

**Payload:**
```json
{
  "id": 123,
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "message": "I'm interested in Umrah packages for next month",
  "created_at": "2025-11-04T10:30:00.000000Z"
}
```

**Fields:**
- `id` (integer): Unique inquiry ID in our database
- `name` (string): Customer name
- `email` (string): Customer email
- `phone` (string): Customer phone number
- `message` (string): Customer's inquiry message
- `created_at` (string): ISO 8601 timestamp

---

#### Type 2: Package-Specific Inquiry (from Package Details Page)

**Payload:**
```json
{
  "id": 124,
  "name": "Jane Smith",
  "email": "jane@example.com",
  "phone": "+1987654321",
  "message": "I would like to book this package for 4 people",
  "created_at": "2025-11-04T11:15:00.000000Z",
  "package_details": {
    "package_name": "Premium Umrah Package 15 Days",
    "pricing": {
      "double": "2500",
      "triple": "2200",
      "quad": "2000",
      "currency": "USD"
    },
    "duration": {
      "nights_makkah": "7",
      "nights_madina": "6",
      "total_nights": "13"
    },
    "hotels": {
      "makkah": "Swissotel Makkah",
      "madina": "Pullman Zamzam Madina"
    },
    "services": {
      "transportation": "Private AC Bus",
      "visa": "30 Days Umrah Visa"
    },
    "inclusions": {
      "breakfast": true,
      "dinner": true,
      "visa": true,
      "ticket": true,
      "roundtrip": true,
      "ziyarat": true,
      "guide": true
    }
  }
}
```

**Additional Fields in Package-Specific Inquiries:**

| Field Path | Type | Description | Example |
|------------|------|-------------|---------|
| `package_details` | object | Package information (only present if inquiry from package page) | See below |
| `package_details.package_name` | string | Name of the package customer inquired about | "Premium Umrah Package 15 Days" |
| `package_details.pricing.double` | string | Price for double occupancy | "2500" |
| `package_details.pricing.triple` | string | Price for triple occupancy | "2200" |
| `package_details.pricing.quad` | string | Price for quad occupancy | "2000" |
| `package_details.pricing.currency` | string | Currency code | "USD" |
| `package_details.duration.nights_makkah` | string | Number of nights in Makkah | "7" |
| `package_details.duration.nights_madina` | string | Number of nights in Madinah | "6" |
| `package_details.duration.total_nights` | string | Total nights in package | "13" |
| `package_details.hotels.makkah` | string | Hotel name in Makkah | "Swissotel Makkah" |
| `package_details.hotels.madina` | string | Hotel name in Madinah | "Pullman Zamzam Madina" |
| `package_details.services.transportation` | string | Transportation service description | "Private AC Bus" |
| `package_details.services.visa` | string | Visa service description | "30 Days Umrah Visa" |
| `package_details.inclusions.breakfast` | boolean | Breakfast included | true |
| `package_details.inclusions.dinner` | boolean | Dinner included | true |
| `package_details.inclusions.visa` | boolean | Visa service included | true |
| `package_details.inclusions.ticket` | boolean | Flight ticket included | true |
| `package_details.inclusions.roundtrip` | boolean | Round-trip ticket | true |
| `package_details.inclusions.ziyarat` | boolean | Ziyarat tours included | true |
| `package_details.inclusions.guide` | boolean | Free Umrah guide included | true |

---

## 🔄 Webhook Flow

```
Customer submits inquiry
         ↓
Inquiry saved to database
         ↓
Webhook payload prepared
         ↓
Signature calculated
         ↓
POST request sent to your webhook URL
         ↓
Your booking portal receives & validates
         ↓
(If validation fails, we log but don't retry)
         ↓
Your portal processes inquiry
```

---

## ✅ Response Handling

### Expected Responses

**Success (200-299 status codes):**
```json
{
  "success": true,
  "message": "Inquiry received successfully",
  "booking_id": "your-internal-booking-id"
}
```

**Error (400-599 status codes):**
```json
{
  "success": false,
  "error": "Error message",
  "code": "ERROR_CODE"
}
```

### Our Behavior

- **Timeout:** 8 seconds
- **Retry Policy:** No automatic retries (best-effort delivery)
- **Logging:** We log failed deliveries for manual review
- **Manual Retry:** Admins can manually trigger retry via `/api/inquiries/{id}/forward-webhook`

---

## 📥 Your Endpoint Implementation

### Required Implementation

Your webhook endpoint must:

1. ✅ **Validate Signature** - Verify X-Webhook-Signature header
2. ✅ **Check Timestamp** - Reject old requests (>5 minutes old recommended)
3. ✅ **Handle Idempotency** - Use Idempotency-Key to prevent duplicate processing
4. ✅ **Parse Payload** - Handle both payload types (with/without package_details)
5. ✅ **Return Response** - Return 200-299 on success, 4xx/5xx on error

### Sample Implementation (Node.js/Express)

```javascript
const express = require('express');
const crypto = require('crypto');
const app = express();

app.use(express.json());

const WEBHOOK_SECRET = process.env.WEBHOOK_SECRET;
const processedKeys = new Set(); // For idempotency

function verifySignature(timestamp, bodyJson, signature) {
    const message = `${timestamp}.${bodyJson}`;
    const expectedSignature = crypto
        .createHmac('sha256', WEBHOOK_SECRET)
        .update(message)
        .digest('hex');
    return crypto.timingSafeEqual(
        Buffer.from(signature),
        Buffer.from(expectedSignature)
    );
}

app.post('/api/webhooks/inquiries', (req, res) => {
    const timestamp = req.headers['x-webhook-timestamp'];
    const signature = req.headers['x-webhook-signature'];
    const idempotencyKey = req.headers['idempotency-key'];
    const bodyJson = JSON.stringify(req.body);

    // 1. Verify signature
    if (!verifySignature(timestamp, bodyJson, signature)) {
        return res.status(401).json({ error: 'Invalid signature' });
    }

    // 2. Check timestamp (reject if >5 minutes old)
    const now = Math.floor(Date.now() / 1000);
    if (Math.abs(now - parseInt(timestamp)) > 300) {
        return res.status(400).json({ error: 'Request too old' });
    }

    // 3. Check idempotency
    if (processedKeys.has(idempotencyKey)) {
        return res.status(200).json({ 
            success: true, 
            message: 'Already processed' 
        });
    }
    processedKeys.add(idempotencyKey);

    // 4. Process inquiry
    const inquiry = req.body;
    
    // Check if this is a package-specific inquiry
    if (inquiry.package_details) {
        console.log('Package inquiry received:', {
            customer: inquiry.name,
            package: inquiry.package_details.package_name,
            pricing: inquiry.package_details.pricing
        });
        // Process package-specific inquiry
        // - Create lead in booking system
        // - Associate with package
        // - Include pricing in quote
        // - Pre-fill package details in booking form
    } else {
        console.log('General inquiry received:', {
            customer: inquiry.name,
            message: inquiry.message
        });
        // Process general inquiry
        // - Create general lead
        // - Assign to sales team
    }

    // 5. Return success
    res.status(200).json({
        success: true,
        message: 'Inquiry received successfully',
        booking_id: 'your-internal-id-here'
    });
});

app.listen(3000);
```

---

## 🧪 Testing

### Test Webhook Manually

You can trigger a manual webhook resend for testing:

**Endpoint:** `POST /api/inquiries/{id}/forward-webhook`

**Headers:**
```
X-Api-Key: <admin-api-key>
Content-Type: application/json
```

**Example:**
```bash
curl -X POST https://www.mtumrah.com/api/inquiries/123/forward-webhook \
  -H "X-Api-Key: your-admin-api-key" \
  -H "Content-Type: application/json"
```

**Response:**
```json
{
  "success": true,
  "status": 200,
  "body": "Response from your webhook"
}
```

### Test Data Examples

#### General Inquiry Test Data
```json
{
  "id": 1,
  "name": "Test Customer",
  "email": "test@example.com",
  "phone": "+1234567890",
  "message": "Test inquiry message",
  "created_at": "2025-11-04T10:00:00.000000Z"
}
```

#### Package Inquiry Test Data
```json
{
  "id": 2,
  "name": "Test Customer 2",
  "email": "test2@example.com",
  "phone": "+1987654321",
  "message": "I want to book this package for my family",
  "created_at": "2025-11-04T11:00:00.000000Z",
  "package_details": {
    "package_name": "Luxury Umrah Package 14 Days",
    "pricing": {
      "double": "3500",
      "triple": "3200",
      "quad": "3000",
      "currency": "USD"
    },
    "duration": {
      "nights_makkah": "7",
      "nights_madina": "5",
      "total_nights": "12"
    },
    "hotels": {
      "makkah": "Hilton Makkah Convention Hotel",
      "madina": "Millennium Taiba Hotel"
    },
    "services": {
      "transportation": "Private Luxury Bus",
      "visa": "Multiple Entry Umrah Visa"
    },
    "inclusions": {
      "breakfast": true,
      "dinner": true,
      "visa": true,
      "ticket": true,
      "roundtrip": true,
      "ziyarat": true,
      "guide": true
    }
  }
}
```

---

## 📊 Field Specifications

### Core Fields (Always Present)

| Field | Type | Required | Max Length | Description |
|-------|------|----------|------------|-------------|
| `id` | integer | Yes | - | Unique inquiry ID in our database |
| `name` | string | Yes | 255 | Customer's full name |
| `email` | string | Yes | 255 | Customer's email address (validated) |
| `phone` | string | Yes | 255 | Customer's phone number |
| `message` | string | Yes | - | Customer's inquiry message/question |
| `created_at` | string | Yes | - | ISO 8601 timestamp (UTC) |

### Package Details Object (Optional)

**Only present when inquiry submitted from package details page.**

| Field Path | Type | Always Present? | Description |
|------------|------|-----------------|-------------|
| `package_details` | object | No | Only present for package-specific inquiries |
| `package_details.package_name` | string | If parent exists | Name of the package |
| `package_details.pricing` | object | If parent exists | Pricing information |
| `package_details.pricing.double` | string/null | If parent exists | Price for 2 people sharing |
| `package_details.pricing.triple` | string/null | If parent exists | Price for 3 people sharing |
| `package_details.pricing.quad` | string/null | If parent exists | Price for 4 people sharing |
| `package_details.pricing.currency` | string | If parent exists | Currency code (USD, EUR, etc.) |
| `package_details.duration` | object | If parent exists | Duration information |
| `package_details.duration.nights_makkah` | string/null | If parent exists | Nights in Makkah |
| `package_details.duration.nights_madina` | string/null | If parent exists | Nights in Madinah |
| `package_details.duration.total_nights` | string/null | If parent exists | Total nights |
| `package_details.hotels` | object | If parent exists | Hotel information |
| `package_details.hotels.makkah` | string/null | If parent exists | Hotel name in Makkah |
| `package_details.hotels.madina` | string/null | If parent exists | Hotel name in Madinah |
| `package_details.services` | object | If parent exists | Service information |
| `package_details.services.transportation` | string/null | If parent exists | Transportation description |
| `package_details.services.visa` | string/null | If parent exists | Visa service description |
| `package_details.inclusions` | object | If parent exists | What's included |
| `package_details.inclusions.breakfast` | boolean | If parent exists | Breakfast included |
| `package_details.inclusions.dinner` | boolean | If parent exists | Dinner included |
| `package_details.inclusions.visa` | boolean | If parent exists | Visa service included |
| `package_details.inclusions.ticket` | boolean | If parent exists | Flight ticket included |
| `package_details.inclusions.roundtrip` | boolean | If parent exists | Round-trip ticket |
| `package_details.inclusions.ziyarat` | boolean | If parent exists | Ziyarat tours included |
| `package_details.inclusions.guide` | boolean | If parent exists | Free guide included |

---

## 🔄 Backward Compatibility

### Important Notes:

1. **Existing integrations continue to work** - The `package_details` field is **optional** and only appears for package-specific inquiries

2. **Your existing code doesn't need changes** - If you're already processing the base fields (id, name, email, phone, message), it will continue to work

3. **Gradual adoption** - You can add package details handling at your own pace:
   ```javascript
   // Existing code - still works!
   const { id, name, email, phone, message } = webhookPayload;
   createLead(name, email, phone, message);
   
   // New enhanced code - optional
   if (webhookPayload.package_details) {
       // Handle package-specific inquiry
       attachPackageInfo(leadId, webhookPayload.package_details);
   }
   ```

---

## 💡 Use Cases

### Use Case 1: General Inquiry Handling

**Scenario:** Customer submits inquiry from home page

**Payload received:**
```json
{
  "id": 100,
  "name": "Ahmed Ali",
  "email": "ahmed@example.com",
  "phone": "+966501234567",
  "message": "I need information about Ramadan packages",
  "created_at": "2025-11-04T12:00:00.000000Z"
}
```

**Your system should:**
1. Create a general lead
2. Assign to available sales agent
3. No package pre-selection

---

### Use Case 2: Package-Specific Inquiry

**Scenario:** Customer viewing "Premium Umrah 15 Days" package clicks inquiry form

**Payload received:**
```json
{
  "id": 101,
  "name": "Fatima Khan",
  "email": "fatima@example.com",
  "phone": "+1416987654",
  "message": "I want to book this for 6 people in December",
  "created_at": "2025-11-04T12:30:00.000000Z",
  "package_details": {
    "package_name": "Premium Umrah Package 15 Days",
    "pricing": { "double": "3500", "triple": "3200", "quad": "3000", "currency": "USD" },
    "duration": { "nights_makkah": "8", "nights_madina": "5", "total_nights": "13" },
    "hotels": { "makkah": "Swissotel Makkah", "madina": "Dar Al Eiman Royal" },
    "services": { "transportation": "Private Luxury Bus", "visa": "Multiple Entry Visa" },
    "inclusions": {
      "breakfast": true, "dinner": true, "visa": true, 
      "ticket": true, "roundtrip": true, "ziyarat": true, "guide": true
    }
  }
}
```

**Your system should:**
1. Create a high-priority lead (customer showed strong intent)
2. Pre-populate booking form with package details:
   - Package: "Premium Umrah Package 15 Days"
   - Travelers: 6 people (from message)
   - Price: 6 × $3000 (quad) = $18,000 base estimate
3. Include hotel preferences in notes
4. Flag as "Hot Lead" - customer is on package details page
5. Assign to specialized Umrah sales team

---

## 🛠️ Recommended Implementation

### Database Schema Suggestion

For your booking portal database:

```sql
CREATE TABLE inquiries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    marwah_inquiry_id INT NOT NULL,  -- Our inquiry.id
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    
    -- Package details (nullable)
    package_name VARCHAR(255) NULL,
    package_pricing JSON NULL,
    package_duration JSON NULL,
    package_hotels JSON NULL,
    package_services JSON NULL,
    package_inclusions JSON NULL,
    
    -- Metadata
    lead_status VARCHAR(50) DEFAULT 'new',
    assigned_to INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Idempotency
    UNIQUE KEY idx_marwah_id (marwah_inquiry_id)
);
```

### Processing Logic Suggestion

```javascript
async function processInquiry(webhookPayload) {
    const baseData = {
        marwah_inquiry_id: webhookPayload.id,
        customer_name: webhookPayload.name,
        customer_email: webhookPayload.email,
        customer_phone: webhookPayload.phone,
        message: webhookPayload.message,
    };

    // Check if package-specific
    if (webhookPayload.package_details) {
        const pkg = webhookPayload.package_details;
        
        return await createPackageInquiry({
            ...baseData,
            package_name: pkg.package_name,
            package_pricing: JSON.stringify(pkg.pricing),
            package_duration: JSON.stringify(pkg.duration),
            package_hotels: JSON.stringify(pkg.hotels),
            package_services: JSON.stringify(pkg.services),
            package_inclusions: JSON.stringify(pkg.inclusions),
            lead_status: 'hot',  // Package inquiries are higher priority
            priority: 'high',
        });
    } else {
        return await createGeneralInquiry({
            ...baseData,
            lead_status: 'new',
            priority: 'normal',
        });
    }
}
```

---

## 📧 Notification Suggestions

### For Sales Team

When package-specific inquiry received:

**Subject:** 🔥 Hot Lead: Customer Inquiring About {package_name}

**Body:**
```
New inquiry from package details page!

Customer: {name}
Email: {email}
Phone: {phone}

Package Interested In: {package_name}
Price Range: ${pricing.quad} - ${pricing.double} per person
Duration: {total_nights} nights ({nights_makkah} Makkah, {nights_madina} Madinah)

Hotels:
- Makkah: {hotels.makkah}
- Madinah: {hotels.madina}

Inclusions: [List all true inclusions]

Customer Message:
{message}

Action Required: Contact customer ASAP - they are actively viewing this package!
```

---

## 🔍 Troubleshooting

### Common Issues

**Issue 1: Signature Verification Fails**
```
Cause: Secret mismatch or incorrect signature calculation
Fix: Verify both systems use same secret and algorithm
```

**Issue 2: Duplicate Processing**
```
Cause: Not checking Idempotency-Key header
Fix: Implement idempotency check using the key
```

**Issue 3: Missing package_details**
```
Cause: Treating it as required field
Fix: Handle as optional - check if exists before accessing
```

**Issue 4: Timeout Errors**
```
Cause: Your endpoint takes >8 seconds to respond
Fix: Return 200 immediately, process asynchronously
```

---

## 📞 Support & Contact

### Integration Support

If you need help integrating:
1. Test webhook endpoint ready: Send us your URL
2. Secret exchange: We'll securely share webhook secret
3. Testing: We can trigger test webhooks
4. Monitoring: We can check webhook delivery logs

### Webhook Monitoring

We maintain logs of all webhook attempts:
- Success/failure status
- Response status codes
- Response bodies
- Timestamps

Request webhook delivery logs: Contact Marwah Travels IT team

---

## 📅 Changelog

### Version 2.0 - November 4, 2025
**Added:**
- ✅ Optional `package_details` object for package-specific inquiries
- ✅ Pricing information (double, triple, quad occupancy)
- ✅ Duration details (nights in Makkah, Madinah, total)
- ✅ Hotel names for both cities
- ✅ Service descriptions (transportation, visa)
- ✅ Inclusion flags (7 boolean fields)

**Backward Compatible:**
- ✅ All existing integrations continue to work
- ✅ `package_details` is optional
- ✅ Base fields unchanged

### Version 1.0 - Previous
**Initial release:**
- Basic inquiry fields: id, name, email, phone, message, created_at

---

## ✅ Integration Checklist

For booking portal developers:

- [ ] Webhook endpoint created and deployed
- [ ] Signature verification implemented
- [ ] Timestamp validation implemented
- [ ] Idempotency handling implemented
- [ ] Test endpoint provided to Marwah Travels
- [ ] Webhook secret shared securely
- [ ] Test webhook received successfully
- [ ] General inquiries processing correctly
- [ ] Package-specific inquiries processing correctly
- [ ] Error handling implemented
- [ ] Logging/monitoring set up
- [ ] Alert system configured for failed webhooks
- [ ] Documentation reviewed and questions resolved

---

## 📊 Expected Volume

### Current Traffic:
- **General inquiries:** ~50-100 per month
- **Package inquiries:** ~20-40 per month (new feature)
- **Peak times:** 9 AM - 5 PM EST, Monday-Friday
- **Off-peak:** Evenings and weekends (lower volume)

### Webhook Reliability:
- **Success rate:** Target >95%
- **Timeout:** 8 seconds
- **Retry:** Manual only (no automatic retries)

---

## 🎯 Summary

### What Changed:
1. Inquiry API now accepts **19 additional optional fields** for package details
2. Webhook payload now includes `package_details` object when inquiry from package page
3. **Fully backward compatible** - existing integrations unaffected

### What You Need to Do:
1. Update your webhook endpoint to handle optional `package_details` object
2. Implement signature verification (if not already done)
3. Handle both inquiry types (general vs package-specific)
4. Test with sample data provided above

### Timeline:
- **Deployment:** November 4, 2025
- **Testing window:** 1 week
- **Full production:** November 11, 2025

---

**Questions? Contact: Marwah Travels IT Team**

---

## Appendix A: Complete Payload Examples

### Example 1: Minimal General Inquiry
```json
{
  "id": 50,
  "name": "John",
  "email": "john@test.com",
  "phone": "123456",
  "message": "Info please",
  "created_at": "2025-11-04T10:00:00.000000Z"
}
```

### Example 2: Complete Package Inquiry
```json
{
  "id": 51,
  "name": "Sarah Johnson",
  "email": "sarah.johnson@email.com",
  "phone": "+14165551234",
  "message": "I'm interested in booking this package for my family of 4 during Ramadan 2026. Can you provide more details about the hotels and visa process?",
  "created_at": "2025-11-04T14:30:00.000000Z",
  "package_details": {
    "package_name": "Ramadan Special Umrah Package 2026",
    "pricing": {
      "double": "4500",
      "triple": "4200",
      "quad": "3800",
      "currency": "USD"
    },
    "duration": {
      "nights_makkah": "10",
      "nights_madina": "8",
      "total_nights": "18"
    },
    "hotels": {
      "makkah": "Fairmont Makkah Clock Royal Tower",
      "madina": "Oberoi Madina"
    },
    "services": {
      "transportation": "Private AC Coach with WiFi",
      "visa": "90 Days Multiple Entry Umrah Visa"
    },
    "inclusions": {
      "breakfast": true,
      "dinner": true,
      "visa": true,
      "ticket": true,
      "roundtrip": true,
      "ziyarat": true,
      "guide": true
    }
  }
}
```

---

## Appendix B: Error Codes

If your webhook returns an error, use these standard codes:

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `SIGNATURE_INVALID` | 401 | Signature verification failed |
| `TIMESTAMP_EXPIRED` | 400 | Request timestamp too old |
| `ALREADY_PROCESSED` | 200 | Idempotency - already processed |
| `VALIDATION_ERROR` | 422 | Invalid data format |
| `SERVER_ERROR` | 500 | Internal processing error |
| `SERVICE_UNAVAILABLE` | 503 | Booking system temporarily down |

---

**End of Documentation**

