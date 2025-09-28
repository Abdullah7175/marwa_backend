# Marwah Travels API Documentation

## Base URL
```
http://98.82.201.1:8000/api
```

## Authentication
Some endpoints require authentication using Laravel Sanctum tokens.

---

## 📁 Categories API

### Get All Categories
```http
GET /api/categories
```

**Response:**
```json
[
  {
    "id": 1,
    "name": "Umrah Packages",
    "status": "active",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
]
```

### Get Single Category
```http
GET /api/categories/{id}
```

### Create Category
```http
POST /api/categories/create
Content-Type: application/json

{
  "name": "New Category Name"
}
```

### Update Category
```http
PUT /api/categories/{id}
Content-Type: application/json

{
  "name": "Updated Category Name",
  "status": "active"
}
```

### Delete Category
```http
DELETE /api/categories/{id}
```

---

## 📦 Packages API

### Get All Packages
```http
GET /api/packages
```

**Response:**
```json
[
  {
    "id": 1,
    "name": "Luxury Umrah Package",
    "price_single": "1695",
    "what_to_expect": "Experience luxury Umrah...",
    "package_image": "/storage/package_images/image.jpg",
    "category": {
      "id": 1,
      "name": "Umrah Packages"
    }
  }
]
```

### Get Single Package
```http
GET /api/packages/{id}
```

### Create Package
```http
POST /api/packages/create
Content-Type: multipart/form-data

{
  "name": "Package Name",
  "price_single": 1695,
  "what_to_expect": "Package description",
  "price_quad": 1595,
  "price_double": 1645,
  "price_tripple": 1620,
  "currency": "USD",
  "nights_makkah": 4,
  "nights_madina": 3,
  "nights": 7,
  "is_roundtrip": true,
  "ziyarat": true,
  "guide": true,
  "email": "info@example.com",
  "whatsapp": "+1234567890",
  "phone": "+1234567890",
  "main_points": "Key features",
  "hotel_makkah_enabled": true,
  "hotel_madina_enabled": true,
  "visa_enabled": true,
  "ticket_enabled": true,
  "breakfast_enabled": true,
  "dinner_enabled": false,
  "transport_enabled": true,
  "category_id": 1,
  "package_image": [FILE],
  "hotel_makkah_image": [FILE],
  "hotel_madina_image": [FILE],
  "visa_image": [FILE],
  "trans_image": [FILE]
}
```

### Update Package
```http
PUT /api/packages/{id}
Content-Type: multipart/form-data

{
  "id": 1,
  "name": "Updated Package Name",
  // ... other fields
  "package_image": [FILE] // Optional - only if updating image
}
```

### Delete Package
```http
DELETE /api/packages/{id}
```

---

## 🏨 Hotels API

### Get All Hotels
```http
GET /api/hotels
```

### Get Single Hotel
```http
GET /api/hotels/{id}
```

### Create Hotel
```http
POST /api/hotels/create
Content-Type: multipart/form-data

{
  "name": "Hotel Name",
  "location": "Makkah",
  "charges": 200,
  "rating": 5,
  "description": "Hotel description",
  "currency": "USD",
  "email": "hotel@example.com",
  "phone": "+1234567890",
  "breakfast_enabled": true,
  "dinner_enabled": false,
  "image": [FILE]
}
```

### Update Hotel
```http
PUT /api/hotels/{id}
Content-Type: multipart/form-data

{
  "id": 1,
  "name": "Updated Hotel Name",
  // ... other fields
  "image": [FILE] // Optional - only if updating image
}
```

### Delete Hotel
```http
DELETE /api/hotels/{id}
```

---

## 📝 Blogs API

### Get All Blogs
```http
GET /api/blogs
```

**Response:**
```json
[
  {
    "id": 1,
    "title": "Blog Title",
    "image": "/storage/blogs_images/image.jpg",
    "elements": [
      {
        "id": 1,
        "element_type": "heading",
        "value": "Main Heading",
        "blog_id": 1
      },
      {
        "id": 2,
        "element_type": "image",
        "value": "/storage/blogs_images/element_image.jpg",
        "blog_id": 1
      }
    ]
  }
]
```

### Get Single Blog
```http
GET /api/blogs/{id}
```

### Create Blog
```http
POST /api/blogs/create
Content-Type: multipart/form-data

{
  "title": "Blog Title",
  "image": [FILE],
  "elements": [
    "{\"element_type\":\"heading\",\"value\":\"Main Heading\"}",
    "{\"element_type\":\"subheading\",\"value\":\"Sub Heading\"}",
    "{\"element_type\":\"points\",\"value\":\"Point 1, Point 2, Point 3\"}",
    "{\"element_type\":\"image\",\"value\":\"image_field_name\"}"
  ],
  "image_field_name": [FILE] // For image elements
}
```

### Update Blog
```http
PUT /api/blogs/{id}
Content-Type: multipart/form-data

{
  "title": "Updated Blog Title",
  "image": [FILE], // Optional
  "elements": [
    "{\"element_type\":\"heading\",\"value\":\"Updated Heading\"}"
  ]
}
```

### Delete Blog
```http
DELETE /api/blogs/{id}
```

---

## 📧 Inquiries API

### Get All Inquiries
```http
GET /api/inquiries
```

### Get Single Inquiry
```http
GET /api/inquiries/{id}
```

### Create Inquiry
```http
POST /api/inquiries/create
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "message": "Inquiry message"
}
```

### Update Inquiry
```http
PUT /api/inquiries/{id}
Content-Type: application/json

{
  "name": "Updated Name",
  "email": "updated@example.com",
  "phone": "+1234567890",
  "message": "Updated message"
}
```

### Delete Inquiry
```http
DELETE /api/inquiries/{id}
```

---

## ⭐ Reviews/Testimonials API

### Get All Reviews
```http
GET /api/reviews
```

### Get Single Review
```http
GET /api/reviews/{id}
```

### Create Review
```http
POST /api/reviews/create
Content-Type: multipart/form-data

{
  "user_name": "Customer Name",
  "detail": "Review content",
  "video_url": "https://youtube.com/watch?v=..." // Optional
}
```

### Update Review
```http
PUT /api/reviews/{id}
Content-Type: multipart/form-data

{
  "user_name": "Updated Name",
  "detail": "Updated review content",
  "video_url": "https://youtube.com/watch?v=..."
}
```

### Delete Review
```http
DELETE /api/reviews/{id}
```

---

## 🎯 Custom Packages API

### Get All Custom Packages
```http
GET /api/custom-packages
```

### Get Single Custom Package
```http
GET /api/custom-packages/{id}
```

### Create Custom Package
```http
POST /api/custom-packages/create
Content-Type: multipart/form-data

{
  "user_name": "Customer Name",
  "tour_days": 7,
  "flight_from": "New York",
  "country": "USA",
  "city": "New York",
  "no_of_travelers": 2,
  "travelers_visa_details": "Visa details",
  "phone": "+1234567890",
  "email": "customer@example.com",
  "additional_comments": "Special requests",
  "signature_image_url": [FILE],
  "total_amount_hotels": 1500.00,
  "hotel_makkah_id": 1,
  "hotel_madina_id": 2
}
```

### Update Custom Package
```http
PUT /api/custom-packages/{id}
Content-Type: multipart/form-data

{
  // Same fields as create
}
```

### Delete Custom Package
```http
DELETE /api/custom-packages/{id}
```

---

## 🔍 SEO Settings API

### Get Page SEO Settings
```http
GET /api/seo/page
```

### Get Blog SEO Settings
```http
GET /api/seo/blog
```

### Get Package SEO Settings
```http
GET /api/seo/package
```

### Update Page SEO
```http
POST /api/seo/page/update
Content-Type: application/json

{
  "page_name": "home",
  "meta_title": "Page Title",
  "meta_description": "Page description",
  "meta_keywords": "keyword1, keyword2",
  "og_title": "OG Title",
  "og_description": "OG Description",
  "og_image": "/images/og-image.jpg",
  "twitter_title": "Twitter Title",
  "twitter_description": "Twitter Description",
  "twitter_image": "/images/twitter-image.jpg",
  "structured_data": "{\"@context\":\"https://schema.org\"}"
}
```

### Update Blog SEO
```http
POST /api/seo/blog/update
Content-Type: application/json

{
  "blog_id": 1,
  "meta_title": "Blog SEO Title",
  "meta_description": "Blog SEO description",
  // ... other SEO fields
}
```

### Update Package SEO
```http
POST /api/seo/package/update
Content-Type: application/json

{
  "package_id": 1,
  "meta_title": "Package SEO Title",
  "meta_description": "Package SEO description",
  // ... other SEO fields
}
```

### Get All SEO Settings
```http
GET /api/seo/all
```

### Delete Page SEO
```http
DELETE /api/seo/page/delete
```

---

## 🌐 Public Web APIs

### Get Packages for Website
```http
GET /api/web/packs
```

### Get Blogs for Website
```http
GET /api/web/blogs
```

### Submit Public Inquiry
```http
POST /api/web/inquiry/submit
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "message": "Inquiry message"
}
```

---

## 📁 File Serving API

### Serve Images
```http
GET /api/files?path=/storage/package_images/image.jpg
```

**Alternative Direct Access:**
```http
GET /storage/package_images/image.jpg
```

---

## 🔐 Authentication APIs

### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```

### Get Current User
```http
GET /api/user
Authorization: Bearer {token}
```

### Create User
```http
POST /api/users
Content-Type: application/json

{
  "name": "Admin User",
  "email": "admin@example.com",
  "password": "password"
}
```

### Delete User
```http
DELETE /api/users/{id}
```

---

## 📊 Panel APIs

### Get All Categories (Panel)
```http
GET /api/panel/categories
```

### Get All Hotels (Panel)
```http
GET /api/panel/hotels
```

### Update Category (Panel)
```http
POST /api/panel/category/update
Content-Type: application/json

{
  "id": 1,
  "name": "Updated Category",
  "status": "active"
}
```

### Update Hotel (Panel)
```http
POST /api/panel/hotel/update
Content-Type: application/json

{
  "id": 1,
  "name": "Updated Hotel",
  "location": "Makkah",
  "charges": 200,
  "rating": 5,
  "description": "Updated description"
}
```

---

## 🚨 Error Responses

All APIs return consistent error responses:

### Validation Error (422)
```json
{
  "errors": {
    "field_name": ["The field name field is required."]
  }
}
```

### Not Found Error (404)
```json
{
  "error": "Resource not found"
}
```

### Server Error (500)
```json
{
  "error": "Internal server error"
}
```

---

## 📝 Notes

1. **Image Upload**: All image uploads use `multipart/form-data` content type
2. **File Storage**: Images are stored in Laravel's storage system and served via `/storage/` URLs
3. **Validation**: All endpoints include proper validation with detailed error messages
4. **Relationships**: Packages include category relationships, Blogs include elements
5. **Soft Deletes**: Categories use soft deletes (status = 'delete')
6. **SEO Support**: All major entities support SEO meta fields
7. **Public APIs**: Separate endpoints for public website consumption

---

## 🔧 Testing the APIs

You can test these APIs using:
- **Postman**: Import the endpoints and test with sample data
- **cURL**: Use command line for quick testing
- **Frontend Integration**: Use these endpoints in your React/Next.js frontend

---

## 📞 Support

For API support or questions, contact the development team.
