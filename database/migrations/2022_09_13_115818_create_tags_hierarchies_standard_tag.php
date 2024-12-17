<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsHierarchiesStandardTag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_hierarchies_standard_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('standard_tag_id');
            $table->unsignedBigInteger('tag_hierarchy_id');

            $table->foreign('standard_tag_id')->on('standard_tags')->references('id');
            $table->foreign('tag_hierarchy_id')->on('tag_hierarchies')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags_hierarchies_standard_tag');
    }
}
