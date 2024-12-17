<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLevelFourDreamCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dream_cars', function (Blueprint $table) {
            $table->unsignedBigInteger('level_four_tag_id')->nullable();
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
            $table->dropColumn('level_four_tag_id');
        });
    }
}
