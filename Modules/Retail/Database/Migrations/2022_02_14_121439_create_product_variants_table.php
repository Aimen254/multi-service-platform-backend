<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->string('external_id', 50)->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('color_id')->nullable();
            $table->unsignedBigInteger('size_id')->nullable();
            $table->string('title')->nullable();
            $table->string('sku')->nullable();
            $table->string('price')->nullable();
            $table->string('discount_price')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('custom_size')->nullable();
            $table->string('custom_color')->nullable();
            $table->enum('stock_status', ['in_stock', 'out_of_stock'])->default('in_stock');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('previous_status', ['active', 'inactive'])->default('inactive');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('cascade');
            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_variants');
    }
}
