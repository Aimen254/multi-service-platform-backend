<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeadlineSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('headline_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('level_two_tag_id')->nullable();
            $table->string('type');
            $table->timestamps();
        });

        Schema::table('headline_settings', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('standard_tags')->onDelete('cascade');
            $table->foreign('level_two_tag_id')->references('id')->on('standard_tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('headline_settings');
    }
}
