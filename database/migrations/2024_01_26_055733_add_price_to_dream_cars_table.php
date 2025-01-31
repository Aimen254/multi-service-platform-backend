<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceToDreamCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dream_cars', function (Blueprint $table) {
            $table->double('min_price')->nullable()->after('to');
            $table->double('max_price')->nullable()->after('min_price');
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
            $table->dropColumn('min_price');
            $table->dropColumn('max_price');
        });
    }
}
