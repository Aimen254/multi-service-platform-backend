<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductAutomotivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_automotives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('maker_id')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->unsignedBigInteger('exterior_color_id')->nullable();
            $table->unsignedBigInteger('interior_color_id')->nullable();
            $table->unsignedBigInteger('body_type_id')->nullable();
            $table->enum('type', ['used', 'new'])->default('used');
            $table->date('year');
            $table->string('trim');
            $table->string('mileage');
            $table->string('vin');
            $table->string('mpg')->nullable();
            $table->string('stock_no')->nullable();
            $table->text('sellers_notes')->nullable();
            $table->string('engine')->nullable();
            $table->enum('transmission', ['manual', 'automatic'])->default('automatic');
            $table->string('drivetrain')->nullable();
            $table->enum('fuel_type', ['gas', 'diesel', 'electric'])->default('gas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_automotives');
    }
}
