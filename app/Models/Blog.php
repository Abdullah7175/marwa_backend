<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'image', 
        'body',
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

    /**
     * Get the default meta title if none is set
     */
    public function getMetaTitleAttribute($value)
    {
        return $value ?: $this->title;
    }

    /**
     * Get the default meta description if none is set
     */
    public function getMetaDescriptionAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        // Generate description from body content
        $body = strip_tags($this->body);
        return strlen($body) > 160 ? substr($body, 0, 157) . '...' : $body;
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
        return $value ?: $this->image;
    }
}
