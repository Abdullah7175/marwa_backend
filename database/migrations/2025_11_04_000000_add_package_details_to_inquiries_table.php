<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add package-related fields to inquiries table for package detail page inquiries
     */
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            // Package information (all nullable - only filled when inquiry from package details page)
            $table->string('package_name')->nullable()->after('message');
            $table->string('price_double')->nullable()->after('package_name');
            $table->string('price_triple')->nullable()->after('price_double');
            $table->string('price_quad')->nullable()->after('price_triple');
            $table->string('currency')->nullable()->after('price_quad');
            $table->string('nights_makkah')->nullable()->after('currency');
            $table->string('nights_madina')->nullable()->after('nights_makkah');
            $table->string('total_nights')->nullable()->after('nights_madina');
            $table->string('hotel_makkah_name')->nullable()->after('total_nights');
            $table->string('hotel_madina_name')->nullable()->after('hotel_makkah_name');
            $table->string('transportation_title')->nullable()->after('hotel_madina_name');
            $table->string('visa_title')->nullable()->after('transportation_title');
            $table->boolean('breakfast_included')->nullable()->after('visa_title');
            $table->boolean('dinner_included')->nullable()->after('breakfast_included');
            $table->boolean('visa_included')->nullable()->after('dinner_included');
            $table->boolean('ticket_included')->nullable()->after('visa_included');
            $table->boolean('roundtrip')->nullable()->after('ticket_included');
            $table->boolean('ziyarat_included')->nullable()->after('roundtrip');
            $table->boolean('guide_included')->nullable()->after('ziyarat_included');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};

