<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicleReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('make_id');
            $table->unsignedBigInteger('model_id');
            $table->unsignedBigInteger('review_id');
            $table->date('year')->nullable();
            $table->tinyInteger('overall_rating')->nullable();
            $table->tinyInteger('comfort')->nullable();
            $table->tinyInteger('interior_design')->nullable();
            $table->tinyInteger('performance')->nullable();
            $table->tinyInteger('value_for_the_money')->nullable();
            $table->tinyInteger('exterior_styling')->nullable();
            $table->tinyInteger('reliability')->nullable();
            $table->string('title')->nullable();
            $table->enum('recommendation', ['yes', 'no'])->nullable();
            $table->enum('condition', ['new', 'used'])->nullable();
            $table->enum('purpose', ['commuting', 'transporting_family', 'having_fun'])->nullable();
            $table->enum('reviewer', ['current_owner', 'former_owner'])->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active')->nullable();
            $table->timestamps();

            $table->foreign('make_id')->references('id')->on('standard_tags');
            $table->foreign('model_id')->references('id')->on('standard_tags');
            $table->foreign('review_id')->references('id')->on('reviews')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_reviews');
    }
}
