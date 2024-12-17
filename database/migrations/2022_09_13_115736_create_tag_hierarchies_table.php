<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagHierarchiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_hierarchies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('L1');
            $table->unsignedBigInteger('L2')->nullable();
            $table->unsignedBigInteger('L3')->nullable();
            $table->unsignedBigInteger('L4')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('level_type', 5)->nullable();
            $table->boolean('is_multiple')->nullable()->default(false);
            $table->timestamps();

            $table->foreign('L1')->on('standard_tags')->references('id')->onUpdate('cascade');
            $table->foreign('L2')->on('standard_tags')->references('id')->onUpdate('cascade');
            $table->foreign('L3')->on('standard_tags')->references('id')->onUpdate('cascade');
            $table->foreign('L4')->on('standard_tags')->references('id')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tag_hierarchies');
    }
}
