<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->id();
            $table->string('user_name');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email');
            $table->string('payment_method_id');
            $table->string('brand');
            $table->string('country');
            $table->integer('expiry_month');
            $table->integer('expiry_year');
            $table->string('last_four');
            $table->string('live_mode');
            $table->string('customer_id');
            $table->string('token');
            $table->boolean('default')->default(0);
            $table->boolean('save_card')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credit_cards');
    }
}
