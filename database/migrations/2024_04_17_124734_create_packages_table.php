<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('price_single')->nullable();
            $table->text('what_to_expect')->nullable();
            $table->string('price_quad')->nullable();
            $table->string('price_double')->nullable();
            $table->string('price_tripple')->nullable();
            $table->string('currency')->nullable();
            $table->string('hotel_makkah_name')->nullable();
            $table->string('hotel_madina_name')->nullable();
            $table->text('hotel_makkah_detail')->nullable();
            $table->text('hotel_madina_detail')->nullable();
            $table->text('hotel_madina_image')->nullable();
            $table->text('hotel_makkah_image')->nullable();
            $table->string('trans_title')->nullable();
            $table->text('trans_detail')->nullable();
            $table->text('trans_image')->nullable();
            $table->string('visa_title')->nullable();
            $table->text('visa_detail')->nullable();
            $table->text('visa_image')->nullable();
            $table->unsignedInteger('nights_makkah');
            $table->unsignedInteger('nights_madina');
            $table->unsignedInteger('nights');
            $table->boolean('is_roundtrip');
            $table->boolean('ziyarat');
            $table->boolean('guide');
            $table->string('email')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('phone')->nullable();
            $table->string('main_points')->nullable();

            $table->boolean('hotel_makkah_enabled');
            $table->boolean('hotel_madina_enabled');
            $table->boolean('visa_enabled');
            $table->boolean('ticket_enabled');
            $table->boolean('breakfast_enabled');
            $table->boolean('dinner_enabled');
            $table->string('visa_duration')->nullable();
            $table->text('package_image')->nullable();
            $table->boolean('transport_enabled');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('packages');
    }
}
