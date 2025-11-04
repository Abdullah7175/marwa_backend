<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'email', 
        'phone', 
        'message',
        // Package details (optional - only from package detail page)
        'package_name',
        'price_double',
        'price_triple',
        'price_quad',
        'currency',
        'nights_makkah',
        'nights_madina',
        'total_nights',
        'hotel_makkah_name',
        'hotel_madina_name',
        'transportation_title',
        'visa_title',
        'breakfast_included',
        'dinner_included',
        'visa_included',
        'ticket_included',
        'roundtrip',
        'ziyarat_included',
        'guide_included',
    ];

    protected $casts = [
        'breakfast_included' => 'boolean',
        'dinner_included' => 'boolean',
        'visa_included' => 'boolean',
        'ticket_included' => 'boolean',
        'roundtrip' => 'boolean',
        'ziyarat_included' => 'boolean',
        'guide_included' => 'boolean',
    ];
}
