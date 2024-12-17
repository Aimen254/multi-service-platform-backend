<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToDreamCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dream_cars', function (Blueprint $table) {
            $table->index('module_id');
            $table->index('model_id');
            $table->index('make_id');
            $table->index('level_four_tag_id');
            $table->index('to');
            $table->index('from');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dream_cars', function (Blueprint $table) {
            $table->dropIndex(['module_id', 'model_id', 'make_id', 'level_four_tag_id', 'to', 'from']);
        });
    }
}
