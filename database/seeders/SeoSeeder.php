<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SeoSettings;

class SeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seoSettings = [
            [
                'page_name' => 'home',
                'meta_title' => 'Marwah Travels - Premium Umrah Packages | Makkah & Madina Tours',
                'meta_description' => 'Discover premium Umrah packages with Marwah Travels. Professional Umrah services including visa, flights, hotels in Makkah & Madina. Book your spiritual journey today.',
                'meta_keywords' => 'Umrah packages, Umrah travel, Makkah tours, Madina tours, Umrah visa, Umrah flights, Umrah hotels, Islamic travel, religious tourism',
                'og_title' => 'Marwah Travels - Premium Umrah Packages',
                'og_description' => 'Professional Umrah services including visa, flights, hotels in Makkah & Madina. Book your spiritual journey today.',
                'og_image' => '/logo2.png',
                'twitter_title' => 'Marwah Travels - Premium Umrah Packages',
                'twitter_description' => 'Professional Umrah services including visa, flights, hotels in Makkah & Madina.',
                'twitter_image' => '/logo2.png',
                'structured_data' => [
                    "@context" => "https://schema.org",
                    "@type" => "TravelAgency",
                    "name" => "Marwah Travels",
                    "description" => "Professional Umrah travel services",
                    "url" => "https://www.mtumrah.com",
                    "logo" => "https://www.mtumrah.com/logo2.png",
                    "address" => [
                        "@type" => "PostalAddress",
                        "streetAddress" => "15636 71ST AVE 28B",
                        "addressLocality" => "FLUSHING",
                        "addressRegion" => "NEW YORK",
                        "postalCode" => "11367",
                        "addressCountry" => "US"
                    ],
                    "contactPoint" => [
                        "@type" => "ContactPoint",
                        "telephone" => "+16463895945",
                        "contactType" => "customer service",
                        "availableLanguage" => "English"
                    ]
                ]
            ],
            [
                'page_name' => 'luxury-umrah-packages',
                'meta_title' => 'Luxury Umrah Packages | Premium Umrah Tours | Marwah Travels',
                'meta_description' => 'Experience luxury Umrah packages with Marwah Travels. Premium hotels, VIP services, and exclusive experiences for your spiritual journey to Makkah and Madina.',
                'meta_keywords' => 'Luxury Umrah, Premium Umrah, VIP Umrah, 5-star Umrah, Luxury Islamic travel, Premium religious tourism',
                'og_title' => 'Luxury Umrah Packages | Premium Umrah Tours',
                'og_description' => 'Experience luxury Umrah packages with premium hotels and VIP services for your spiritual journey.',
                'og_image' => '/images/luxury-umrah.jpg',
                'twitter_title' => 'Luxury Umrah Packages | Premium Umrah Tours',
                'twitter_description' => 'Experience luxury Umrah packages with premium hotels and VIP services.',
                'twitter_image' => '/images/luxury-umrah.jpg',
                'structured_data' => [
                    "@context" => "https://schema.org",
                    "@type" => "TouristTrip",
                    "name" => "Luxury Umrah Packages",
                    "description" => "Premium Umrah packages with luxury accommodations",
                    "touristType" => "Religious Tourism",
                    "destinations" => ["Makkah", "Madina"]
                ]
            ],
            [
                'page_name' => 'blogs',
                'meta_title' => 'Umrah Travel Blog | Latest Updates & Travel Tips | Marwah Travels',
                'meta_description' => 'Stay updated with the latest Umrah travel information, tips, and updates from Marwah Travels. Expert advice for your spiritual journey to Makkah and Madina.',
                'meta_keywords' => 'Umrah blog, Umrah travel tips, Umrah updates, Islamic travel blog, Makkah Madina travel guide',
                'og_title' => 'Umrah Travel Blog | Latest Updates & Travel Tips',
                'og_description' => 'Stay updated with the latest Umrah travel information and expert tips for your spiritual journey.',
                'og_image' => '/images/blog-header.jpg',
                'twitter_title' => 'Umrah Travel Blog | Latest Updates & Travel Tips',
                'twitter_description' => 'Stay updated with the latest Umrah travel information and expert tips.',
                'twitter_image' => '/images/blog-header.jpg',
                'structured_data' => [
                    "@context" => "https://schema.org",
                    "@type" => "Blog",
                    "name" => "Umrah Travel Blog",
                    "description" => "Latest updates and travel tips for Umrah travelers"
                ]
            ],
            [
                'page_name' => 'testimonials',
                'meta_title' => 'Customer Testimonials | Umrah Travel Reviews | Marwah Travels',
                'meta_description' => 'Read authentic customer testimonials and reviews from satisfied Umrah travelers. See why thousands choose Marwah Travels for their spiritual journey.',
                'meta_keywords' => 'Umrah testimonials, Umrah reviews, customer feedback, Umrah travel reviews, Marwah Travels reviews',
                'og_title' => 'Customer Testimonials | Umrah Travel Reviews',
                'og_description' => 'Read authentic customer testimonials from satisfied Umrah travelers.',
                'og_image' => '/images/testimonials-header.jpg',
                'twitter_title' => 'Customer Testimonials | Umrah Travel Reviews',
                'twitter_description' => 'Read authentic customer testimonials from satisfied Umrah travelers.',
                'twitter_image' => '/images/testimonials-header.jpg',
                'structured_data' => [
                    "@context" => "https://schema.org",
                    "@type" => "Review",
                    "name" => "Customer Testimonials",
                    "description" => "Authentic reviews from Umrah travelers"
                ]
            ],
            [
                'page_name' => 'about-us',
                'meta_title' => 'About Marwah Travels | Leading Umrah Travel Agency | New York',
                'meta_description' => 'Learn about Marwah Travels, a leading Umrah travel agency based in New York. Years of experience in providing professional Umrah services worldwide.',
                'meta_keywords' => 'About Marwah Travels, Umrah agency New York, Umrah travel company, Islamic travel agency, Marwah Travels history',
                'og_title' => 'About Marwah Travels | Leading Umrah Travel Agency',
                'og_description' => 'Learn about our years of experience in providing professional Umrah services worldwide.',
                'og_image' => '/logo2.png',
                'twitter_title' => 'About Marwah Travels | Leading Umrah Travel Agency',
                'twitter_description' => 'Learn about our years of experience in providing professional Umrah services.',
                'twitter_image' => '/logo2.png',
                'structured_data' => [
                    "@context" => "https://schema.org",
                    "@type" => "Organization",
                    "name" => "Marwah Travels",
                    "description" => "Leading Umrah travel agency",
                    "foundingDate" => "2020",
                    "location" => [
                        "@type" => "Place",
                        "address" => [
                            "@type" => "PostalAddress",
                            "addressLocality" => "FLUSHING",
                            "addressRegion" => "NEW YORK"
                        ]
                    ]
                ]
            ]
        ];

        foreach ($seoSettings as $setting) {
            SeoSettings::updateOrCreate(
                ['page_name' => $setting['page_name']],
                $setting
            );
        }

        $this->command->info('SEO settings seeded successfully!');
    }
}
