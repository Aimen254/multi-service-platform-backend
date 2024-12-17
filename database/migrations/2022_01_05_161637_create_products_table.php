<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('business_id');
            $table->string('external_id')->nullable();
            $table->string('name');
            $table->double('price');
            $table->string('sku')->nullable();
            $table->text('description')->nullable();
            $table->integer('stock')->nullable();
            $table->double('weight')->nullable();
            $table->string('weight_unit')->nullable();
            $table->integer('package_count')->default(0);
            $table->integer('available_items')->default(0);
            $table->double('discount_price')->nullable();
            $table->date('discount_start_date')->nullable();
            $table->date('discount_end_date')->nullable();
            $table->string('tax_type')->nullable();
            $table->integer('tax_percentage')->nullable();
            $table->enum('stock_status', ['in_stock', 'out_of_stock'])->default('in_stock');
            $table->string('status')->default('active');
            $table->string('previous_status')->default('active');
            $table->string('type')->nullable();
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
            $table->double('discount_value')->nullable();
            $table->boolean('is_featured')->default(0);
            $table->boolean('is_deliverable')->default(1);
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
