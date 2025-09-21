<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoSettings extends Model
{
    use HasFactory;

    protected $table = 'seo_settings';

    protected $fillable = [
        'page_name',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'structured_data'
    ];

    protected $casts = [
        'structured_data' => 'array'
    ];

    /**
     * Get SEO settings for a specific page
     */
    public static function getForPage($pageName)
    {
        return static::where('page_name', $pageName)->first();
    }

    /**
     * Get default meta title if none is set
     */
    public function getMetaTitleAttribute($value)
    {
        return $value ?: 'Marwah Travels - Premium Umrah Packages';
    }

    /**
     * Get default meta description if none is set
     */
    public function getMetaDescriptionAttribute($value)
    {
        return $value ?: 'Discover premium Umrah packages with Marwah Travels. Professional Umrah services including visa, flights, hotels in Makkah & Madina.';
    }

    /**
     * Get default og title if none is set
     */
    public function getOgTitleAttribute($value)
    {
        return $value ?: $this->getMetaTitleAttribute($value);
    }

    /**
     * Get default og description if none is set
     */
    public function getOgDescriptionAttribute($value)
    {
        return $value ?: $this->getMetaDescriptionAttribute($value);
    }

    /**
     * Get default og image if none is set
     */
    public function getOgImageAttribute($value)
    {
        return $value ?: '/logo2.png';
    }

    /**
     * Get default Twitter title if none is set
     */
    public function getTwitterTitleAttribute($value)
    {
        return $value ?: $this->getMetaTitleAttribute($value);
    }

    /**
     * Get default Twitter description if none is set
     */
    public function getTwitterDescriptionAttribute($value)
    {
        return $value ?: $this->getMetaDescriptionAttribute($value);
    }

    /**
     * Get default Twitter image if none is set
     */
    public function getTwitterImageAttribute($value)
    {
        return $value ?: $this->getOgImageAttribute($value);
    }
}
