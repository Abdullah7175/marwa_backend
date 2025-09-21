-- =====================================================
-- MARWAH TRAVELS DUMMY DATA
-- Based on Client Images - Umrah Packages & Testimonials
-- =====================================================

USE marwah_travels;

-- =====================================================
-- CATEGORIES DATA
-- =====================================================

INSERT INTO categories (name, status, created_at, updated_at) VALUES
('Umrah Packages', 'active', NOW(), NOW()),
('Hajj Packages', 'active', NOW(), NOW()),
('Group Tours', 'active', NOW(), NOW()),
('Luxury Packages', 'active', NOW(), NOW());

-- =====================================================
-- HOTELS DATA
-- =====================================================

INSERT INTO hotels (name, location, charges, rating, image, description, created_at, updated_at) VALUES
('Anwar Al Madinah Mövenpick', 'Madina', '$200/night', '5', '/images/hotels/anwar-madinah.jpg', 'Luxury 5-star hotel near Prophet\'s Mosque', NOW(), NOW()),
('Swissôtel Hotel Makkah', 'Makkah', '$180/night', '5', '/images/hotels/swissotel-makkah.jpg', 'Premium hotel with Kaaba view', NOW(), NOW()),
('Dar Al Eiman Al Haram Hotel', 'Makkah', '$150/night', '4', '/images/hotels/dar-al-eiman.jpg', 'Comfortable hotel near Grand Mosque', NOW(), NOW()),
('Hilton Makkah Convention Hotel', 'Makkah', '$160/night', '4', '/images/hotels/hilton-makkah.jpg', 'Business-class hotel with modern amenities', NOW(), NOW());

-- =====================================================
-- PACKAGES DATA (Based on Client Images)
-- =====================================================

INSERT INTO packages (
    name, price_single, what_to_expect, price_quad, price_double, price_tripple, currency,
    hotel_makkah_name, hotel_madina_name, hotel_makkah_detail, hotel_madina_detail,
    hotel_madina_image, hotel_makkah_image, trans_title, trans_detail, trans_image,
    visa_title, visa_detail, visa_image, nights_makkah, nights_madina, nights,
    is_roundtrip, ziyarat, guide, email, whatsapp, phone, main_points,
    hotel_makkah_enabled, hotel_madina_enabled, visa_enabled, ticket_enabled,
    breakfast_enabled, dinner_enabled, visa_duration, package_image, transport_enabled,
    category_id, meta_title, meta_description, meta_keywords, og_title, og_description,
    og_image, twitter_title, twitter_description, twitter_image,
    created_at, updated_at
) VALUES
(
    'Luxury Umrah Package (07 Nights)',
    '1695',
    'Experience luxury Umrah with premium accommodations and VIP services',
    '1595',
    '1645',
    '1620',
    'USD',
    'Swissôtel Hotel Makkah',
    'Anwar Al Madinah Mövenpick',
    '5-star luxury hotel with Kaaba view, modern amenities, and exceptional service',
    'Premium hotel near Prophet\'s Mosque with elegant rooms and world-class facilities',
    '/images/hotels/anwar-madinah-luxury.jpg',
    '/images/hotels/swissotel-makkah-luxury.jpg',
    'Premium Transportation',
    'Air-conditioned luxury buses with professional drivers',
    '/images/transport/luxury-bus.jpg',
    'Express Visa Processing',
    'Fast-track visa processing with dedicated support',
    '/images/visa/express-visa.jpg',
    4, 3, 7,
    1, 1, 1,
    'info@marwahtravels.com',
    '+16463895945',
    '+16463895945',
    'Luxury accommodations, VIP services, Express visa, Professional guide',
    1, 1, 1, 1, 1, 0,
    '30 days',
    '/images/packages/luxury-umrah-7-nights.jpg',
    1,
    1,
    'Luxury Umrah Package 7 Nights | Premium Umrah Tours',
    'Experience luxury Umrah with premium hotels and VIP services for 7 nights',
    'luxury umrah, premium umrah, VIP umrah, 7 nights umrah',
    'Luxury Umrah Package 7 Nights',
    'Premium Umrah experience with luxury accommodations',
    '/images/packages/luxury-umrah-7-nights.jpg',
    'Luxury Umrah Package 7 Nights',
    'Premium Umrah experience with luxury accommodations',
    '/images/packages/luxury-umrah-7-nights.jpg',
    NOW(), NOW()
),
(
    'Premium Group Umrah Package (07 Nights)',
    '1595',
    'Join our premium group Umrah package with comfortable accommodations',
    '1495',
    '1545',
    '1520',
    'USD',
    'Hilton Makkah Convention Hotel',
    'Dar Al Eiman Al Haram Hotel',
    '4-star hotel with modern facilities and excellent location',
    'Comfortable hotel near Grand Mosque with good amenities',
    '/images/hotels/dar-al-eiman-premium.jpg',
    '/images/hotels/hilton-makkah-premium.jpg',
    'Group Transportation',
    'Comfortable group transportation with experienced drivers',
    '/images/transport/group-bus.jpg',
    'Standard Visa Processing',
    'Regular visa processing with full support',
    '/images/visa/standard-visa.jpg',
    4, 3, 7,
    1, 1, 1,
    'info@marwahtravels.com',
    '+16463895945',
    '+16463895945',
    'Group accommodations, Standard visa, Professional guide, Group transportation',
    1, 1, 1, 1, 1, 0,
    '30 days',
    '/images/packages/premium-group-umrah-7-nights.jpg',
    1,
    2,
    'Premium Group Umrah Package 7 Nights | Group Umrah Tours',
    'Join our premium group Umrah package with comfortable accommodations for 7 nights',
    'group umrah, premium group umrah, 7 nights group umrah',
    'Premium Group Umrah Package 7 Nights',
    'Premium group Umrah experience with comfortable accommodations',
    '/images/packages/premium-group-umrah-7-nights.jpg',
    'Premium Group Umrah Package 7 Nights',
    'Premium group Umrah experience with comfortable accommodations',
    '/images/packages/premium-group-umrah-7-nights.jpg',
    NOW(), NOW()
),
(
    'Luxury Group Umrah Package (10 Nights)',
    '1895',
    'Extended luxury Umrah experience with premium accommodations',
    '1795',
    '1845',
    '1820',
    'USD',
    'Swissôtel Hotel Makkah',
    'Anwar Al Madinah Mövenpick',
    '5-star luxury hotel with Kaaba view and premium services',
    'Premium hotel near Prophet\'s Mosque with extended stay benefits',
    '/images/hotels/anwar-madinah-extended.jpg',
    '/images/hotels/swissotel-makkah-extended.jpg',
    'Luxury Group Transportation',
    'Premium group transportation with luxury vehicles',
    '/images/transport/luxury-group-bus.jpg',
    'Express Visa Processing',
    'Fast-track visa processing with dedicated support',
    '/images/visa/express-visa.jpg',
    6, 4, 10,
    1, 1, 1,
    'info@marwahtravels.com',
    '+16463895945',
    '+16463895945',
    'Extended luxury stay, VIP services, Express visa, Professional guide',
    1, 1, 1, 1, 1, 0,
    '30 days',
    '/images/packages/luxury-group-umrah-10-nights.jpg',
    1,
    4,
    'Luxury Group Umrah Package 10 Nights | Extended Umrah Tours',
    'Extended luxury Umrah experience with premium accommodations for 10 nights',
    'luxury group umrah, extended umrah, 10 nights umrah, luxury umrah',
    'Luxury Group Umrah Package 10 Nights',
    'Extended luxury Umrah experience with premium accommodations',
    '/images/packages/luxury-group-umrah-10-nights.jpg',
    'Luxury Group Umrah Package 10 Nights',
    'Extended luxury Umrah experience with premium accommodations',
    '/images/packages/luxury-group-umrah-10-nights.jpg',
    NOW(), NOW()
);

-- =====================================================
-- REVIEWS/TESTIMONIALS DATA (Based on Client Images)
-- =====================================================

INSERT INTO reviews (user_name, detail, video_url, created_at, updated_at) VALUES
(
    'Mr Arslan Ali',
    'Their impeccable service and attention to detail made our Umrah journey truly memorable. The luxury accommodations and professional guidance exceeded our expectations.',
    '/videos/testimonials/arslan-ali-testimonial.mp4',
    NOW(), NOW()
),
(
    'Mr Saad Sarwat',
    'Marwah Travels provided exceptional care throughout our Umrah journey. The premium package included everything we needed, and the staff was incredibly helpful.',
    '/videos/testimonials/saad-sarwat-testimonial.mp4',
    NOW(), NOW()
),
(
    'ABDUR REHMAN',
    'Marwah Travels Umrah for organizing a smooth and hassle-free Umrah experience. The group coordination was excellent, and we felt well taken care of throughout.',
    '/videos/testimonials/abdur-rehman-testimonial.mp4',
    NOW(), NOW()
),
(
    'Mrs Fatima Ahmed',
    'The luxury Umrah package was worth every penny. The hotels were exceptional, and the VIP services made our spiritual journey comfortable and memorable.',
    '/videos/testimonials/fatima-ahmed-testimonial.mp4',
    NOW(), NOW()
),
(
    'Mr Hassan Khan',
    'Professional service from start to finish. The visa processing was smooth, accommodations were excellent, and the guide was knowledgeable and helpful.',
    '/videos/testimonials/hassan-khan-testimonial.mp4',
    NOW(), NOW()
);

-- =====================================================
-- BLOGS DATA
-- =====================================================

INSERT INTO blogs (title, image, body, meta_title, meta_description, meta_keywords, og_title, og_description, og_image, twitter_title, twitter_description, twitter_image, created_at, updated_at) VALUES
(
    'Complete Guide to Umrah: Everything You Need to Know',
    '/images/blog/umrah-guide.jpg',
    'Umrah is a sacred pilgrimage to Makkah that can be performed at any time of the year. Unlike Hajj, which has specific dates, Umrah offers flexibility for Muslims worldwide to fulfill this spiritual obligation. This comprehensive guide covers everything from preparation to completion of your Umrah journey.

## What is Umrah?

Umrah, often called the "lesser pilgrimage," is a voluntary Islamic pilgrimage to Makkah. While not mandatory like Hajj, it holds great spiritual significance and is highly recommended for Muslims who are physically and financially able to perform it.

## Essential Requirements for Umrah

### 1. Valid Passport and Visa
- Ensure your passport is valid for at least 6 months
- Obtain a Saudi Arabia visa specifically for Umrah
- Complete all required documentation

### 2. Physical and Mental Preparation
- Consult with your doctor if you have health concerns
- Prepare mentally for the spiritual journey
- Learn the proper rituals and supplications

### 3. Financial Planning
- Budget for travel, accommodation, and other expenses
- Consider package deals for better value
- Plan for additional costs like shopping and meals

## Step-by-Step Umrah Process

### 1. Ihram
- Enter the state of Ihram at the designated Miqat
- Wear the prescribed clothing
- Make the intention for Umrah

### 2. Tawaf
- Circumambulate the Kaaba seven times
- Start and end at the Black Stone
- Maintain proper etiquette and respect

### 3. Sa\'i
- Walk between Safa and Marwah seven times
- Follow the footsteps of Hajar (AS)
- Maintain spiritual focus throughout

### 4. Tahallul
- Complete the Umrah rituals
- Exit the state of Ihram
- Offer prayers of gratitude

## Best Time to Perform Umrah

While Umrah can be performed year-round, certain times offer advantages:
- **Ramadan**: Highly rewarding spiritually
- **Winter months**: More comfortable weather
- **Off-peak seasons**: Less crowded, better accommodations

## Accommodation Tips

### Makkah Hotels
- Choose hotels close to the Grand Mosque
- Consider luxury options for comfort
- Book in advance during peak seasons

### Madina Hotels
- Stay near the Prophet\'s Mosque
- Look for hotels with good amenities
- Consider the distance to historical sites

## Transportation

### Airport Transfers
- Pre-arrange transportation from the airport
- Consider group transfers for cost savings
- Ensure reliable and comfortable vehicles

### Local Transportation
- Use hotel shuttles when available
- Consider walking for short distances
- Use authorized transportation services

## Essential Items to Pack

### Clothing
- Comfortable Ihram clothing
- Modest clothing for other times
- Comfortable walking shoes

### Personal Items
- Prayer mat and compass
- Personal hygiene items
- Medications if needed

### Documents
- Passport and visa copies
- Travel insurance documents
- Emergency contact information

## Spiritual Preparation

### Learning Rituals
- Study the proper way to perform Umrah
- Learn the supplications and prayers
- Understand the significance of each step

### Mental Preparation
- Set spiritual goals for your journey
- Prepare for the emotional experience
- Maintain patience and humility

## Common Mistakes to Avoid

1. **Improper Ihram**: Not following the correct Ihram procedures
2. **Rushing Rituals**: Not taking time for proper reflection
3. **Poor Planning**: Not booking accommodations in advance
4. **Ignoring Health**: Not considering physical limitations
5. **Overpacking**: Bringing unnecessary items

## Post-Umrah Reflection

After completing Umrah:
- Reflect on the spiritual experience
- Maintain the spiritual momentum
- Share your experience with family
- Plan for future pilgrimages if possible

## Conclusion

Performing Umrah is a deeply spiritual experience that requires proper preparation and understanding. By following this guide and choosing the right travel partner like Marwah Travels, you can ensure a meaningful and comfortable Umrah journey.

Remember, the key to a successful Umrah is not just completing the rituals, but doing so with proper intention, patience, and spiritual awareness.',
    'Complete Guide to Umrah: Everything You Need to Know | Marwah Travels',
    'Comprehensive guide to performing Umrah pilgrimage. Learn about requirements, process, best times, accommodations, and spiritual preparation for your Umrah journey.',
    'Umrah guide, Umrah pilgrimage, Umrah requirements, Umrah process, Umrah tips, Islamic pilgrimage',
    'Complete Guide to Umrah: Everything You Need to Know',
    'Comprehensive guide to performing Umrah pilgrimage with all essential information',
    '/images/blog/umrah-guide.jpg',
    'Complete Guide to Umrah: Everything You Need to Know',
    'Comprehensive guide to performing Umrah pilgrimage with all essential information',
    '/images/blog/umrah-guide.jpg',
    NOW(), NOW()
),
(
    'Top 5 Luxury Hotels Near Kaaba for Your Umrah Journey',
    '/images/blog/luxury-hotels-kaaba.jpg',
    'Choosing the right accommodation for your Umrah journey is crucial for a comfortable and spiritually fulfilling experience. Here are the top 5 luxury hotels near the Kaaba that offer exceptional service and proximity to the Grand Mosque.

## 1. Swissôtel Makkah

### Location and Features
- **Distance from Kaaba**: 0 meters (Direct access)
- **Star Rating**: 5 stars
- **Special Features**: Kaaba view rooms, luxury amenities

### Why Choose Swissôtel Makkah
Swissôtel Makkah offers unparalleled luxury with direct access to the Grand Mosque. The hotel features:
- Spacious rooms with Kaaba views
- World-class dining options
- Premium spa and wellness facilities
- 24/7 concierge services

### Room Types
- **Deluxe Rooms**: Comfortable accommodations with modern amenities
- **Executive Suites**: Spacious suites with separate living areas
- **Presidential Suites**: Ultimate luxury with panoramic Kaaba views

## 2. Anwar Al Madinah Mövenpick

### Location and Features
- **Distance from Prophet\'s Mosque**: 50 meters
- **Star Rating**: 5 stars
- **Special Features**: Premium location, luxury services

### Why Choose Mövenpick Madinah
This luxury hotel offers:
- Direct access to the Prophet\'s Mosque
- Elegant rooms with modern design
- Multiple dining options
- Business center and meeting facilities

### Amenities
- **Fitness Center**: State-of-the-art equipment
- **Spa Services**: Relaxing treatments and therapies
- **Shopping**: On-site retail outlets
- **Transportation**: Complimentary shuttle services

## 3. Hilton Makkah Convention Hotel

### Location and Features
- **Distance from Kaaba**: 200 meters
- **Star Rating**: 4 stars
- **Special Features**: Business facilities, group accommodations

### Why Choose Hilton Makkah
Perfect for business travelers and groups:
- Modern conference facilities
- Spacious group accommodations
- Multiple dining venues
- Professional business services

### Services
- **Meeting Rooms**: Fully equipped conference facilities
- **Group Services**: Specialized group coordination
- **Dining**: Multiple restaurant options
- **Transportation**: Airport and local transfers

## 4. Dar Al Eiman Al Haram Hotel

### Location and Features
- **Distance from Kaaba**: 100 meters
- **Star Rating**: 4 stars
- **Special Features**: Traditional architecture, cultural experience

### Why Choose Dar Al Eiman
Experience authentic Arabian hospitality:
- Traditional architectural design
- Cultural dining experiences
- Comfortable family accommodations
- Personalized service

### Unique Features
- **Cultural Tours**: Guided tours of historical sites
- **Traditional Cuisine**: Authentic Arabian dishes
- **Family Services**: Specialized family amenities
- **Cultural Events**: Regular cultural programs

## 5. Raffles Makkah Palace

### Location and Features
- **Distance from Kaaba**: 150 meters
- **Star Rating**: 5 stars
- **Special Features**: Palace-style luxury, premium services

### Why Choose Raffles Makkah
Ultimate luxury experience:
- Palace-style accommodations
- Personalized butler services
- Premium dining experiences
- Exclusive amenities

### Luxury Services
- **Butler Service**: 24/7 personal assistance
- **Private Dining**: Exclusive dining experiences
- **Luxury Spa**: Premium wellness treatments
- **Concierge**: Comprehensive travel assistance

## Choosing the Right Hotel

### Factors to Consider

#### 1. Proximity to Holy Sites
- **Kaaba Access**: Direct access vs. walking distance
- **Prayer Times**: Convenience for daily prayers
- **Crowd Management**: Less crowded areas during peak times

#### 2. Accommodation Type
- **Solo Travelers**: Single rooms with essential amenities
- **Families**: Family suites with multiple bedrooms
- **Groups**: Group accommodations with common areas

#### 3. Budget Considerations
- **Luxury**: Premium accommodations with all amenities
- **Mid-range**: Comfortable accommodations with good amenities
- **Budget**: Basic accommodations with essential services

#### 4. Special Requirements
- **Accessibility**: Wheelchair accessible rooms and facilities
- **Dietary**: Halal dining options and special dietary needs
- **Health**: Medical facilities and health services

## Booking Tips

### Advance Booking
- **Peak Seasons**: Book 6-12 months in advance
- **Off-peak**: 2-3 months advance booking sufficient
- **Last-minute**: Limited options available

### Package Deals
- **Umrah Packages**: Often include hotel accommodations
- **Group Rates**: Better rates for group bookings
- **Extended Stays**: Discounts for longer stays

### Cancellation Policies
- **Flexible**: Free cancellation options
- **Standard**: Moderate cancellation fees
- **Non-refundable**: Lower rates but no cancellation

## Conclusion

Choosing the right luxury hotel for your Umrah journey can significantly enhance your spiritual experience. Whether you prefer the direct Kaaba access of Swissôtel Makkah or the traditional charm of Dar Al Eiman, each hotel offers unique advantages.

Consider your specific needs, budget, and preferences when making your choice. Remember, the goal is to find accommodations that allow you to focus on your spiritual journey while enjoying comfort and convenience.

For the best deals and personalized recommendations, consult with Marwah Travels, who can help you find the perfect accommodation that meets your needs and budget.',
    'Top 5 Luxury Hotels Near Kaaba for Umrah | Marwah Travels',
    'Discover the best luxury hotels near Kaaba for your Umrah journey. Compare Swissôtel Makkah, Mövenpick Madinah, Hilton Makkah, and more premium accommodations.',
    'luxury hotels Makkah, hotels near Kaaba, Umrah accommodations, luxury Umrah hotels, Makkah hotels',
    'Top 5 Luxury Hotels Near Kaaba for Umrah',
    'Discover the best luxury hotels near Kaaba for your Umrah journey with detailed comparisons',
    '/images/blog/luxury-hotels-kaaba.jpg',
    'Top 5 Luxury Hotels Near Kaaba for Umrah',
    'Discover the best luxury hotels near Kaaba for your Umrah journey',
    '/images/blog/luxury-hotels-kaaba.jpg',
    NOW(), NOW()
);

-- =====================================================
-- SEO SETTINGS DATA
-- =====================================================

INSERT INTO seo_settings (page_name, meta_title, meta_description, meta_keywords, og_title, og_description, og_image, twitter_title, twitter_description, twitter_image, structured_data, created_at, updated_at) VALUES
(
    'home',
    'Marwah Travels - Premium Umrah Packages | Makkah & Madina Tours',
    'Discover premium Umrah packages with Marwah Travels. Professional Umrah services including visa, flights, hotels in Makkah & Madina. Book your spiritual journey today.',
    'Umrah packages, Umrah travel, Makkah tours, Madina tours, Umrah visa, Umrah flights, Umrah hotels, Islamic travel, religious tourism',
    'Marwah Travels - Premium Umrah Packages',
    'Professional Umrah services including visa, flights, hotels in Makkah & Madina. Book your spiritual journey today.',
    '/logo2.png',
    'Marwah Travels - Premium Umrah Packages',
    'Professional Umrah services including visa, flights, hotels in Makkah & Madina.',
    '/logo2.png',
    '{"@context":"https://schema.org","@type":"TravelAgency","name":"Marwah Travels","description":"Professional Umrah travel services","url":"https://www.mtumrah.com","logo":"https://www.mtumrah.com/logo2.png","address":{"@type":"PostalAddress","streetAddress":"15636 71ST AVE 28B","addressLocality":"FLUSHING","addressRegion":"NEW YORK","postalCode":"11367","addressCountry":"US"},"contactPoint":{"@type":"ContactPoint","telephone":"+16463895945","contactType":"customer service","availableLanguage":"English"}}',
    NOW(), NOW()
),
(
    'luxury-umrah-packages',
    'Luxury Umrah Packages | Premium Umrah Tours | Marwah Travels',
    'Experience luxury Umrah packages with Marwah Travels. Premium hotels, VIP services, and exclusive experiences for your spiritual journey to Makkah and Madina.',
    'Luxury Umrah, Premium Umrah, VIP Umrah, 5-star Umrah, Luxury Islamic travel, Premium religious tourism',
    'Luxury Umrah Packages | Premium Umrah Tours',
    'Experience luxury Umrah packages with premium hotels and VIP services for your spiritual journey.',
    '/images/luxury-umrah.jpg',
    'Luxury Umrah Packages | Premium Umrah Tours',
    'Experience luxury Umrah packages with premium hotels and VIP services.',
    '/images/luxury-umrah.jpg',
    '{"@context":"https://schema.org","@type":"TouristTrip","name":"Luxury Umrah Packages","description":"Premium Umrah packages with luxury accommodations","touristType":"Religious Tourism","destinations":["Makkah","Madina"]}',
    NOW(), NOW()
),
(
    'testimonials',
    'Customer Testimonials | Umrah Travel Reviews | Marwah Travels',
    'Read authentic customer testimonials and reviews from satisfied Umrah travelers. See why thousands choose Marwah Travels for their spiritual journey.',
    'Umrah testimonials, Umrah reviews, customer feedback, Umrah travel reviews, Marwah Travels reviews',
    'Customer Testimonials | Umrah Travel Reviews',
    'Read authentic customer testimonials from satisfied Umrah travelers.',
    '/images/testimonials-header.jpg',
    'Customer Testimonials | Umrah Travel Reviews',
    'Read authentic customer testimonials from satisfied Umrah travelers.',
    '/images/testimonials-header.jpg',
    '{"@context":"https://schema.org","@type":"Review","name":"Customer Testimonials","description":"Authentic reviews from Umrah travelers"}',
    NOW(), NOW()
);

-- =====================================================
-- SAMPLE INQUIRIES DATA
-- =====================================================

INSERT INTO inquiries (name, email, phone, message, created_at, updated_at) VALUES
('Ahmed Hassan', 'ahmed.hassan@email.com', '+1234567890', 'I am interested in the Luxury Umrah Package for 2 people. Please provide more details about the accommodation and visa processing.', NOW(), NOW()),
('Fatima Ali', 'fatima.ali@email.com', '+1987654321', 'Looking for group Umrah packages for my family of 6. We prefer accommodations near the Grand Mosque.', NOW(), NOW()),
('Mohammed Khan', 'mohammed.khan@email.com', '+1122334455', 'Interested in the 10-night luxury package. Can you provide information about the hotels and transportation?', NOW(), NOW());

-- =====================================================
-- SUCCESS MESSAGE
-- =====================================================

SELECT 'Dummy data inserted successfully! Database is ready with Umrah packages and testimonials.' as Status;
