<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('order_id')->unique();
            $table->morphs('model');
            $table->unsignedBigInteger('business_id')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->unsignedBigInteger('order_status_id')->default(1);
            $table->unsignedBigInteger('billing_id')->nullable();
            $table->unsignedBigInteger('shipping_id')->nullable();
            $table->unsignedBigInteger('mailing_id')->nullable();
            $table->boolean('refunded')->default(0);
            $table->double('amount_refunded')->nullable();
            $table->string('payment_intent_id')->nullable();
            $table->integer('selected_card')->nullable();
            $table->boolean('tax_type')->default(0);
            $table->double('actual_price')->default(0);
            $table->double('discount_price')->default(0);
            $table->string('discount_type')->nullable();
            $table->double('discount_value')->nullable();
            $table->double('total');
            $table->double('delivery_fee')->default(0);
            $table->double('total_tax_price')->nullable();
            $table->double('refunded_delivery_fee')->nullable();
            $table->double('refunded_platform_fee')->nullable();
            $table->string('stripe_decline_code')->nullable();
            $table->string('stripe_error_code')->nullable();
            $table->string('stripe_message')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->text('note')->nullable();
            $table->enum('order_type', ['mail', 'delivery', 'pick_up'])->nullable();
            $table->boolean('charged')->default(0);
            $table->string('platform_fee_type')->nullable();
            $table->string('platform_fee_value')->nullable();
            $table->double('platform_commission')->nullable();
            $table->string('delivery_owner')->nullable();
            $table->boolean('captured')->default(0);
            $table->dateTime('shipping_date')->nullable();
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses');
            $table->foreign('coupon_id')->references('id')->on('coupons');
            $table->foreign('order_status_id')->references('id')->on('order_statuses');
            $table->foreign('mailing_id')->references('id')->on('mailings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
