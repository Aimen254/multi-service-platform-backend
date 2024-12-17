<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStandardTagTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('standard_tag_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('standard_tag_id');
            $table->unsignedBigInteger('tag_id');

            $table->foreign('standard_tag_id')->references('id')->on('standard_tags')
                ->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('standard_tag_tag');
    }
}
