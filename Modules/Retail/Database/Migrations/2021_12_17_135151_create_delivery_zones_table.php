<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('model');
            $table->double('mileage_fee')->nullable();
            $table->double('extra_mileage_fee')->nullable();
            $table->integer('mileage_distance')->nullable();
            $table->integer('fixed_amount')->nullable();
            $table->integer('percentage_amount')->nullable();
            $table->enum('zone_type', ['circle', 'polygon'])->default('circle');
            $table->integer('fee_type')->nullable();
            $table->tinyInteger('delivery_type')->default(0);
            $table->string('platform_delivery_type', 30)->nullable()->default(null);
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->unsignedBigInteger('radius')->nullable();
            $table->text('polygon')->nullable();
            $table->string('address')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
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
        Schema::dropIfExists('delivery_zones');
    }
}
