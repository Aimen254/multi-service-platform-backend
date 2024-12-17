<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicProfileFollowerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_profile_follower', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('follower_public_profile_id');
            $table->unsignedBigInteger('following_public_profile_id');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();

            $table->foreign('follower_public_profile_id')->references('id')->on('public_profiles')->onDelete('cascade');
            $table->foreign('following_public_profile_id')->references('id')->on('public_profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public_profile_follower');
    }
}
