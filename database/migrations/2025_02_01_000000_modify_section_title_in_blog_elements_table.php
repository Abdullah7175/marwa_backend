<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifySectionTitleInBlogElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blog_elements', function (Blueprint $table) {
            // Change section_title from VARCHAR(255) to TEXT to allow longer section titles/descriptions
            $table->text('section_title')->nullable()->change();
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
            // Revert back to VARCHAR(255)
            $table->string('section_title')->nullable()->change();
        });
    }
}

