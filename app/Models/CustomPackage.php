<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_name',
        'tour_days',
        'flight_from',
        'country',
        'city',
        'no_of_travelers',
        'travelers_visa_details',
        'phone',
        'email',
        'additional_comments',
        'signature_image_url',
        'total_amount_hotels',
        'hotel_makkah_id',
        'hotel_madina_id',
        'nights_in_madina',
        'nights_in_makkah'
    ];
}
