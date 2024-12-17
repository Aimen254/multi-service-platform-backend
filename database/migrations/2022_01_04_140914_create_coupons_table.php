<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->string('code')->unique();
            $table->integer('minimum_purchase')->nullable();
            $table->integer('limit')->nullable();
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
            $table->integer('discount_value');
            $table->date('start_date')->default(Carbon::now())->nullable();
            $table->date('end_date')->nullable();
            $table->enum('coupon_type', ['business', 'category', 'product'])->nullable();
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            $table->unsignedBigInteger('created_by');
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
        Schema::dropIfExists('coupons');
    }
}
