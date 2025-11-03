# Marwah Travels API Documentation

## Base URL
```
https://www.mtumrah.com/api
```

## Authentication
Some endpoints require authentication via Laravel Sanctum. Include the Bearer token in the Authorization header:
```
Authorization: Bearer {token}
```

## Image/Video URL Format
All image and video URLs returned by the API are formatted to be previewable. They will:
- Start with `/storage/` for relative paths
- Or be full URLs starting with `http://` or `https://`

Use these URLs directly in `<img>` or `<video>` tags, or prepend the base URL for full paths.

---

## 1. Packages API

### 1.1 Get All Packages
**GET** `/packages`

**Response:** 200 OK
```json
[
  {
    "id": 1,
    "name": "Umrah Package 1",
    "price_single": "1500",
    "price_double": "1200",
    "price_tripple": "1100",
    "price_quad": "1000",
    "currency": "USD",
    "what_to_expect": "Package description...",
    "main_points": "Key points...",
    "package_image": "/storage/package_images/image.jpg",
    "hotel_makkah_name": "Hotel Name",
    "hotel_makkah_detail": "Hotel details...",
    "hotel_makkah_image": "/storage/package_images/hotel.jpg",
    "hotel_madina_name": "Hotel Name",
    "hotel_madina_detail": "Hotel details...",
    "hotel_madina_image": "/storage/package_images/hotel.jpg",
    "trans_title": "Transportation",
    "trans_detail": "Transport details...",
    "trans_image": "/storage/package_images/trans.jpg",
    "visa_title": "Visa Services",
    "visa_detail": "Visa details...",
    "visa_image": "/storage/package_images/visa.jpg",
    "visa_duration": "30 days",
    "nights_makkah": 5,
    "nights_madina": 4,
    "nights": 9,
    "is_roundtrip": 1,
    "ziyarat": 1,
    "guide": 1,
    "email": "contact@example.com",
    "whatsapp": "+1234567890",
    "phone": "+1234567890",
    "hotel_makkah_enabled": 1,
    "hotel_madina_enabled": 1,
    "visa_enabled": 1,
    "ticket_enabled": 1,
    "breakfast_enabled": 1,
    "dinner_enabled": 1,
    "transport_enabled": 1,
    "category_id": 1,
    "category": {
      "id": 1,
      "name": "Standard",
      "status": "active"
    },
    "meta_title": "SEO Title",
    "meta_description": "SEO Description",
    "meta_keywords": "keywords",
    "og_title": "OG Title",
    "og_description": "OG Description",
    "og_image": "/storage/package_images/og.jpg",
    "twitter_title": "Twitter Title",
    "twitter_description": "Twitter Description",
    "twitter_image": "/storage/package_images/twitter.jpg",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
]
```

### 1.2 Get Single Package
**GET** `/packages/{id}`

**Response:** 200 OK
```json
{
  "id": 1,
  "name": "Umrah Package 1",
  // ... same structure as above
}
```

**Error Response:** 404 Not Found
```json
{
  "error": "Package not found"
}
```

### 1.3 Create Package
**POST** `/packages/create`

**Content-Type:** `multipart/form-data`

**Request Fields:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| name | string | Yes | Package name (max 255) |
| price_single | string | No | Single room price (varchar 255) |
| price_double | string | No | Double room price (varchar 255) |
| price_tripple | string | No | Triple room price (varchar 255) |
| price_quad | string | No | Quad room price (varchar 255) |
| currency | string | No | Currency code (max 255) |
| what_to_expect | text | No | Package description |
| main_points | string | No | Key points (max 255) |
| package_image | file | No | Main package image |
| hotel_makkah_name | string | No | Hotel name in Makkah |
| hotel_makkah_detail | text | No | Hotel details in Makkah |
| hotel_makkah_image | file | No | Hotel image in Makkah |
| hotel_madina_name | string | No | Hotel name in Madina |
| hotel_madina_detail | text | No | Hotel details in Madina |
| hotel_madina_image | file | No | Hotel image in Madina |
| trans_title | string | No | Transportation title |
| trans_detail | text | No | Transportation details |
| trans_image | file | No | Transportation image |
| visa_title | string | No | Visa service title |
| visa_detail | text | No | Visa service details |
| visa_image | file | No | Visa service image |
| visa_duration | string | No | Visa duration |
| nights_makkah | integer | Yes | Nights in Makkah (min: 0) |
| nights_madina | integer | Yes | Nights in Madina (min: 0) |
| nights | integer | Yes | Total nights (min: 0) |
| is_roundtrip | boolean | Yes | Round trip included (0/1) |
| ziyarat | boolean | Yes | Ziyarat included (0/1) |
| guide | boolean | Yes | Guide included (0/1) |
| email | string | No | Contact email |
| whatsapp | string | No | WhatsApp number |
| phone | string | No | Phone number |
| hotel_makkah_enabled | boolean | Yes | Enable Makkah hotel (0/1) |
| hotel_madina_enabled | boolean | Yes | Enable Madina hotel (0/1) |
| visa_enabled | boolean | Yes | Enable visa service (0/1) |
| ticket_enabled | boolean | Yes | Enable ticket service (0/1) |
| breakfast_enabled | boolean | Yes | Breakfast included (0/1) |
| dinner_enabled | boolean | Yes | Dinner included (0/1) |
| transport_enabled | boolean | Yes | Transport included (0/1) |
| category_id | integer | Yes | Category ID (must exist) |
| meta_title | string | No | SEO meta title |
| meta_description | text | No | SEO meta description |
| meta_keywords | string | No | SEO meta keywords |
| og_title | string | No | Open Graph title |
| og_description | text | No | Open Graph description |
| og_image | string | No | Open Graph image URL |
| twitter_title | string | No | Twitter card title |
| twitter_description | text | No | Twitter card description |
| twitter_image | string | No | Twitter card image URL |

**Note:** Boolean fields can be sent as:
- Integer: `0` or `1`
- String: `"0"`, `"1"`, `"true"`, `"false"`, `"on"`
- Boolean: `true` or `false`

**Response:** 201 Created
```json
{
  "message": "Package created successfully",
  "package": {
    // Package object with formatted image URLs
  }
}
```

**Error Response:** 422 Unprocessable Entity
```json
{
  "errors": {
    "field_name": ["Error message"]
  },
  "message": "Validation failed. Please check the errors below.",
  "received_data": { /* submitted data (files excluded) */ }
}
```

### 1.4 Update Package
**PUT** `/packages/{id}`

**Content-Type:** `multipart/form-data`

**Request Fields:** Same as Create Package, plus:
- `id` (required): Package ID

**Note:** All fields are optional except `id`, `name`, `nights_makkah`, `nights_madina`, `nights`, `category_id`, and boolean fields. Images only update if new files are provided.

**Response:** 200 OK
```json
{
  "message": "Package Updated successfully",
  "package": {
    // Updated package object with formatted image URLs
  }
}
```

### 1.5 Delete Package
**DELETE** `/packages/{id}`

**Response:** 200 OK
```json
{
  "message": "Package deleted successfully"
}
```

---

## 2. Blogs API

### 2.1 Get All Blogs
**GET** `/blogs`

**Response:** 200 OK
```json
[
  {
    "id": 1,
    "title": "Blog Title",
    "image": "/storage/blogs_images/image.jpg",
    "body": "Blog body content...",
    "meta_title": "SEO Title",
    "meta_description": "SEO Description",
    "meta_keywords": "keywords",
    "og_title": "OG Title",
    "og_description": "OG Description",
    "og_image": "/storage/blogs_images/og.jpg",
    "twitter_title": "Twitter Title",
    "twitter_description": "Twitter Description",
    "twitter_image": "/storage/blogs_images/twitter.jpg",
    "elements": [
      {
        "id": 1,
        "element_type": "heading",
        "section_title": "Section 1",
        "order": 0,
        "value": "Heading Text",
        "blog_id": 1,
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-01T00:00:00.000000Z"
      },
      {
        "id": 2,
        "element_type": "image",
        "section_title": "Section 1",
        "order": 1,
        "value": "/storage/blogs_images/element_image.jpg",
        "blog_id": 1,
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-01T00:00:00.000000Z"
      }
    ],
    "elements_by_sections": {
      "Section 1": [
        {
          "id": 1,
          "element_type": "heading",
          "section_title": "Section 1",
          "order": 0,
          "value": "Heading Text",
          "blog_id": 1
        },
        {
          "id": 2,
          "element_type": "image",
          "section_title": "Section 1",
          "order": 1,
          "value": "/storage/blogs_images/element_image.jpg",
          "blog_id": 1
        }
      ],
      "main": [
        // Elements without section_title
      ]
    },
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
]
```

### 2.2 Get Single Blog
**GET** `/blogs/{id}`

**Response:** 200 OK (same structure as Get All Blogs)

### 2.3 Create Blog
**POST** `/blogs/create`

**Content-Type:** `multipart/form-data`

**Request Fields:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| title | string | Yes | Blog title (max 255) |
| body | text | Yes | Blog body content (NOT NULL in DB) |
| image | file | No | Main blog image |
| meta_title | string | No | SEO meta title |
| meta_description | text | No | SEO meta description |
| meta_keywords | string | No | SEO meta keywords |
| og_title | string | No | Open Graph title |
| og_description | text | No | Open Graph description |
| og_image | string | No | Open Graph image URL |
| twitter_title | string | No | Twitter card title |
| twitter_description | text | No | Twitter card description |
| twitter_image | string | No | Twitter card image URL |
| elements[0][element_type] | string | No | Element type: `heading`, `subheading`, `paragraph`, `points`, `image`, `divider` |
| elements[0][value] | string/file | No | Element value (text for text elements, file for images) |
| elements[0][section_title] | string | No | Section title (max 255) |
| elements[0][order] | integer | No | Display order (default: 0) |
| elements[1][...] | ... | No | Additional elements |

**Important Notes:**
- For images, use unique field names: `elements[0][value]`, `elements[1][value]`, etc.
- `section_title` groups elements into sections. Elements without `section_title` go in "main" section.
- `order` determines display order within each section.
- `body` field is required and must be sent (even if empty string) due to database constraint.

**Example Request:**
```
title: "My Blog"
body: "Blog description"
image: [file]
elements[0][element_type]: "heading"
elements[0][value]: "First Heading"
elements[0][section_title]: "Introduction"
elements[0][order]: 0
elements[1][element_type]: "image"
elements[1][value]: [file]
elements[1][section_title]: "Introduction"
elements[1][order]: 1
elements[2][element_type]: "paragraph"
elements[2][value]: "Paragraph text"
elements[2][section_title]: "Introduction"
elements[2][order]: 2
```

**Response:** 201 Created
```json
{
  "message": "Blog created successfully",
  "blog": {
    // Blog object with formatted image URLs
  }
}
```

### 2.4 Update Blog
**POST** `/blogs/{id}` or **PUT** `/blogs/{id}`

**Content-Type:** `multipart/form-data`

**Request Fields:** Same as Create Blog, plus:
- For existing elements: send `element_id` or `elements[id][id]` to update
- To keep existing images: send the current URL string
- To update images: send new file

**Important Notes:**
- If updating existing elements, include their IDs
- Existing image URLs should be sent as strings (not files) to keep them
- New images should be sent as files
- All images (main blog image and element images) are formatted for preview

**Response:** 200 OK
```json
{
  "message": "Blog updated successfully",
  "blog": {
    // Updated blog object with formatted image URLs
  }
}
```

### 2.5 Delete Blog
**DELETE** `/blogs/{id}`

**Response:** 200 OK
```json
{
  "message": "Blog deleted successfully"
}
```

---

## 3. Hotels API

### 3.1 Get All Hotels
**GET** `/hotels`

**Response:** 200 OK
```json
[
  {
    "id": 1,
    "name": "Hotel Name",
    "location": "Makkah",
    "charges": "100",
    "charges_numeric": 100,
    "rating": "4.5",
    "image": "/storage/hotel_images/image.jpg",
    "description": "Hotel description",
    "currency": "USD",
    "phone": "+1234567890",
    "email": "hotel@example.com",
    "status": "active",
    "breakfast_enabled": false,
    "dinner_enabled": false,
    "price_per_night": "USD100",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
]
```

### 3.2 Get Single Hotel
**GET** `/hotels/{id}`

**Response:** 200 OK (same structure as above)

### 3.3 Create Hotel
**POST** `/hotels/create`

**Content-Type:** `multipart/form-data`

**Request Fields:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| name | string | Yes | Hotel name (max 255) |
| location | string | Yes | Hotel location (max 255) |
| charges | string | Yes | Hotel charges (max 255) |
| rating | string | Yes | Hotel rating (max 255) |
| image | file | Yes | Hotel image (must be image file) |
| description | string | Yes | Hotel description (max 255) |
| currency | string | No | Currency code (max 255) |
| email | string | No | Contact email (max 255) |
| phone | string | No | Contact phone (max 255) |
| breakfast_enabled | boolean | No | Breakfast included (0/1 or true/false) |
| dinner_enabled | boolean | No | Dinner included (0/1 or true/false) |
| status | string | No | Hotel status (max 255) |

**Note:** Boolean fields can be sent as integers (0/1), strings ("0"/"1"/"true"/"false"/"on"), or actual booleans.

**Response:** 201 Created
```json
{
  "message": "Hotel created successfully",
  "hotel": {
    // Hotel object with formatted image URL
  }
}
```

**Error Response:** 422 Unprocessable Entity
```json
{
  "errors": {
    "field_name": ["Error message"]
  },
  "message": "Validation failed. Please check the errors below.",
  "received_data": { /* submitted data (files excluded) */ }
}
```

**Error Response:** 500 Internal Server Error
```json
{
  "error": "Failed to create hotel",
  "message": "Detailed error message"
}
```

### 3.4 Update Hotel
**PUT** `/hotels/{id}`

**Content-Type:** `multipart/form-data`

**Request Fields:** Same as Create Hotel, plus:
- `id` (required): Hotel ID
- `image` is optional (only updates if new file is provided)

**Response:** 200 OK
```json
{
  "message": "Hotel Updated successfully",
  "hotel": {
    // Updated hotel object with formatted image URL
  }
}
```

**Error Responses:** Same as Create Hotel

### 3.5 Delete Hotel
**DELETE** `/hotels/{id}`

**Response:** 200 OK

---

## 4. Reviews (Testimonials) API

### 4.1 Get All Reviews
**GET** `/reviews`

**Response:** 200 OK
```json
[
  {
    "id": 1,
    "user_name": "John Doe",
    "detail": "Great experience!",
    "video_url": "/storage/videos/testimonial.mp4",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
]
```

### 4.2 Get Single Review
**GET** `/reviews/{id}`

**Response:** 200 OK

### 4.3 Create Review
**POST** `/reviews/create`

**Content-Type:** `multipart/form-data`

**Request Fields:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| user_name | string | Yes | Reviewer name |
| detail | text | Yes | Review content |
| video_url | file/string | No | Video file or URL |

**Response:** 201 Created

### 4.4 Update Review
**PUT** `/reviews/{id}`

**Content-Type:** `multipart/form-data`

**Request Fields:** Same as Create

**Response:** 200 OK

### 4.5 Delete Review
**GET** `/reviews/delete/{id}`

**Response:** 200 OK

---

## 5. Custom Packages API

### 5.1 Get All Custom Packages
**GET** `/custom-packages`

**Response:** 200 OK
```json
[
  {
    "id": 1,
    "user_name": "John Doe",
    "tour_days": 10,
    "flight_from": "New York",
    "country": "USA",
    "city": "New York",
    "no_of_travelers": 2,
    "travelers_visa_details": "Details...",
    "phone": "+1234567890",
    "email": "john@example.com",
    "additional_comments": "Comments...",
    "signature_image_url": "/storage/signature_images/signature.jpg",
    "total_amount_hotels": "5000.00",
    "hotel_makkah_id": 1,
    "hotel_madina_id": 2,
    "hotel_makkah_name": "Hotel Makkah",
    "hotel_madina_name": "Hotel Madina",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
]
```

### 5.2 Get Single Custom Package
**GET** `/custom-packages/{id}`

**Response:** 200 OK

### 5.3 Create Custom Package
**POST** `/custom-packages/create`

**Content-Type:** `multipart/form-data`

**Request Fields:**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| user_name | string | Yes | User name |
| tour_days | integer | Yes | Tour duration in days |
| flight_from | string | Yes | Flight origin |
| country | string | Yes | Country |
| city | string | Yes | City |
| no_of_travelers | integer | Yes | Number of travelers |
| travelers_visa_details | text | No | Visa details |
| phone | string | Yes | Phone number |
| email | string | Yes | Email address |
| additional_comments | text | No | Additional comments |
| signature_image_url | file | Yes | Signature image |
| total_amount_hotels | decimal | Yes | Total amount (8,2) |
| hotel_makkah_id | integer | No | Makkah hotel ID |
| hotel_madina_id | integer | No | Madina hotel ID |

**Response:** 201 Created

### 5.4 Update Custom Package
**PUT** `/custom-packages/{id}`

**Content-Type:** `multipart/form-data`

**Response:** 200 OK

### 5.5 Delete Custom Package
**GET** `/custom-packages/delete/{id}`

**Response:** 200 OK

---

## 6. Categories API

### 6.1 Get All Categories
**GET** `/categories`

**Response:** 200 OK
```json
[
  {
    "id": 1,
    "name": "Standard",
    "status": "active",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
]
```

### 6.2 Get Single Category
**GET** `/categories/{id}`

### 6.3 Create Category
**POST** `/categories/create`

**Request Body (JSON):**
```json
{
  "name": "Standard",
  "status": "active"
}
```

### 6.4 Update Category
**PUT** `/categories/{id}`

### 6.5 Delete Category
**DELETE** `/categories/{id}`

---

## 7. Inquiries API

### 7.1 Get All Inquiries
**GET** `/inquiries`

### 7.2 Get Single Inquiry
**GET** `/inquiries/{id}`

### 7.3 Create Inquiry (Public)
**POST** `/web/inquiry/submit`

**Request Body (JSON):**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "message": "Inquiry message"
}
```

### 7.4 Update Inquiry
**PUT** `/inquiries/{id}`

### 7.5 Delete Inquiry
**DELETE** `/inquiries/{id}`

---

## 8. SEO Settings API

### 8.1 Get Page SEO
**GET** `/seo/page?page_name=home`

**Query Parameters:**
- `page_name` (required): Page name

### 8.2 Get Blog SEO
**GET** `/seo/blog`

### 8.3 Get Package SEO
**GET** `/seo/package`

### 8.4 Get All SEO Settings
**GET** `/seo/all`

### 8.5 Update Page SEO
**POST** `/seo/page/update`

**Request Body (JSON):**
```json
{
  "page_name": "home",
  "meta_title": "Title",
  "meta_description": "Description",
  // ... other SEO fields
}
```

### 8.6 Update Blog SEO
**POST** `/seo/blog/update`

### 8.7 Update Package SEO
**POST** `/seo/package/update`

### 8.8 Delete Page SEO
**DELETE** `/seo/page/delete?page_name=home`

---

## 9. Web Public API

### 9.1 Get Packages (Public)
**GET** `/web/packs`

Returns packages formatted for public display.

### 9.2 Get Blogs (Public)
**GET** `/web/blogs`

Returns blogs with elements formatted for public display.

---

## 10. Panel API

### 10.1 Get All Categories
**GET** `/panel/categories`

### 10.2 Get All Hotels
**GET** `/panel/hotels`

### 10.3 Update Category
**POST** `/panel/category/update`

### 10.4 Update Hotel
**POST** `/panel/hotel/update`

---

## 11. File Serving API

### 11.1 Get File
**GET** `/files?path=package_images/image.jpg`

**Query Parameters:**
- `path` (required): Relative path to file

**Allowed Directories:**
- `package_images/`
- `hotel_images/`
- `blogs_images/`
- `signature_images/`
- `videos/`
- `storage/` (any path starting with storage/)
- `images/` (any path starting with images/)

**Response:** File content with appropriate Content-Type

**Error Responses:**
- 400: Missing path parameter
- 403: Access denied (invalid path or not in allowed directory)
- 404: File not found

---

## Common Error Responses

### 422 Unprocessable Entity (Validation Error)
```json
{
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  },
  "message": "Validation failed. Please check the errors below."
}
```

### 404 Not Found
```json
{
  "error": "Resource not found"
}
```

### 500 Internal Server Error
```json
{
  "error": "Error message",
  "message": "Detailed error message"
}
```

---

## Notes

1. **Image/Video URLs**: All image and video URLs are automatically formatted to be previewable (starting with `/storage/` or full URL).

2. **Multipart Form Data**: Endpoints that accept file uploads require `Content-Type: multipart/form-data`.

3. **Boolean Fields**: Boolean fields can be sent as integers (0/1), strings ("0"/"1"/"true"/"false"/"on"), or actual booleans. The backend normalizes them to 0/1.

4. **Price Fields**: Price fields in packages are stored as `varchar(255)`, not numeric, to support currency symbols and formatting.

5. **Blog Elements**: Blog elements support multiple sections via `section_title`. Elements are ordered by `order` within each section.

6. **Database Schema Match**: All API endpoints match the current database schema exactly, including nullable fields, data types, and constraints.
