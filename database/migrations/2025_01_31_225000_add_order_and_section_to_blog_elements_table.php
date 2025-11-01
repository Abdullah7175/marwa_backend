<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderAndSectionToBlogElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blog_elements', function (Blueprint $table) {
            $table->string('section_title')->nullable()->after('element_type');
            $table->integer('order')->default(0)->after('section_title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blog_elements', function (Blueprint $table) {
            $table->dropColumn(['section_title', 'order']);
        });
    }
}
