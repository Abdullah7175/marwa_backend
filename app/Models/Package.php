<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'price_single',
        'what_to_expect',
        'price_quad',
        'main_points',
        'price_double',
        'price_tripple',
        'currency',
        'hotel_makkah_name',
        'hotel_madina_name',
        'hotel_makkah_detail',
        'hotel_madina_detail',
        'hotel_madina_image',
        'hotel_makkah_image',
        'trans_title',
        'trans_detail',
        'trans_image',
        'visa_title',
        'visa_detail',
        'visa_image',
        'nights_makkah',
        'nights_madina',
        'nights',
        'is_roundtrip',
        'ziyarat',
        'guide',
        'email',
        'whatsapp',
        'phone',
        'hotel_makkah_enabled',
        'hotel_madina_enabled',
        'visa_enabled',
        'ticket_enabled',
        'breakfast_enabled',
        'dinner_enabled',
        'visa_duration',
        'package_image',
        'transport_enabled',
        'category_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the default meta title if none is set
     */
    public function getMetaTitleAttribute($value)
    {
        return $value ?: $this->name;
    }

    /**
     * Get the default meta description if none is set
     */
    public function getMetaDescriptionAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        // Generate description from package details
        $description = "Umrah package: {$this->name} - {$this->nights} nights";
        if ($this->hotel_makkah_name) {
            $description .= " | Hotel in Makkah: {$this->hotel_makkah_name}";
        }
        if ($this->hotel_madina_name) {
            $description .= " | Hotel in Madina: {$this->hotel_madina_name}";
        }
        $description .= " | Starting from {$this->currency}{$this->price_quad}";
        
        return strlen($description) > 160 ? substr($description, 0, 157) . '...' : $description;
    }

    /**
     * Get the default og title if none is set
     */
    public function getOgTitleAttribute($value)
    {
        return $value ?: $this->getMetaTitleAttribute($value);
    }

    /**
     * Get the default og description if none is set
     */
    public function getOgDescriptionAttribute($value)
    {
        return $value ?: $this->getMetaDescriptionAttribute($value);
    }

    /**
     * Get the default og image if none is set
     */
    public function getOgImageAttribute($value)
    {
        return $value ?: $this->package_image;
    }
}
