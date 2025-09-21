<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomPackagesTable extends Migration
{
    public function up()
    {
        Schema::create('custom_packages', function (Blueprint $table) {
            $table->id();
            $table->string('user_name');
            $table->integer('tour_days');
            $table->string('flight_from');
            $table->string('country');
            $table->string('city');
            $table->integer('no_of_travelers');
            $table->text('travelers_visa_details')->nullable();
            $table->string('phone');
            $table->string('email');
            $table->text('additional_comments')->nullable();
            $table->string('signature_image_url');
            $table->decimal('total_amount_hotels', 8, 2);
            $table->integer('hotel_makkah_id');
            $table->integer('hotel_madina_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('custom_packages');
    }
}
