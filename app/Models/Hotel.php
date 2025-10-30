<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'charges', 'rating','status','image','description','phone','email','currency','breakfast_enabled','dinner_enabled'];

    /**
     * Get the currency attribute with default value
     */
    public function getCurrencyAttribute($value)
    {
        return $value ?: 'USD';
    }

    /**
     * Get the charges as a numeric value for calculations
     */
    public function getChargesNumericAttribute()
    {
        // Remove currency symbols and extract numeric value
        $charges = preg_replace('/[^0-9.]/', '', $this->charges);
        return (float) $charges;
    }

    /**
     * Get formatted price per night
     */
    public function getPricePerNightAttribute()
    {
        $currency = $this->currency ?? 'USD';
        $charges = $this->charges_numeric;
        return $currency . $charges;
    }

    /**
     * Ensure rating is always a numeric value (accessor for formatted rating)
     */
    public function getFormattedRatingAttribute()
    {
        $value = $this->attributes['rating'] ?? null;
        if (is_numeric($value)) {
            return (float) $value;
        }
        // Extract numeric rating from string like "4.0/5.0"
        if (preg_match('/(\d+\.?\d*)/', $value, $matches)) {
            return (float) $matches[1];
        }
        return 0.0;
    }

    /**
     * Get status with default
     */
    public function getStatusAttribute($value)
    {
        return $value ?: 'active';
    }

    /**
     * Get breakfast_enabled with default
     */
    public function getBreakfastEnabledAttribute($value)
    {
        return $value ?? false;
    }

    /**
     * Get dinner_enabled with default
     */
    public function getDinnerEnabledAttribute($value)
    {
        return $value ?? false;
    }
}
