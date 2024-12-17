<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStandardTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_standard_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('standard_tag_id');
            $table->unsignedBigInteger('product_id');

            $table->foreign('standard_tag_id')->references('id')->on('standard_tags')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_standard_tag');
    }
}
