<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->enum('type', ['product', 'attribute', 'brand', 'module', 'industry'])
                ->nullable();
            $table->unsignedBigInteger('attribute_id')->nullable();
            $table->unsignedBigInteger('mapped_to')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('priority', [1,2,3,4])->default(4);
            $table->boolean('is_category')->default(false);
            $table->boolean('is_show')->default(true);
            $table->timestamps();

            $table->foreign('mapped_to')->references('id')->on('standard_tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
    }
}
