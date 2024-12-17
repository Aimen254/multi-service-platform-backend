<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_external')->nullable(); // this flag is for user avatar
            $table->date('dob')->nullable();
            $table->string('neighborhood_name')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('user_type')->default('customer');
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_connect_id')->nullable();
            $table->string('stripe_bank_id')->nullable();
            $table->boolean('completed_stripe_onboarding')->default(false);
            $table->boolean('is_social')->default(0);
            $table->string('about')->nullable();
            $table->bigInteger('phone_otp')->nullable();
            $table->bigInteger('email_otp')->nullable();
            $table->bigInteger('otp')->nullable();
            $table->boolean('is_verified')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
