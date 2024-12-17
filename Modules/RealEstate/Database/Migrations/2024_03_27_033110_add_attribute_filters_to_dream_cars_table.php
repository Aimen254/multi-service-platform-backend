<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAttributeFiltersToDreamCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dream_cars', function (Blueprint $table) {
            $table->unsignedBigInteger('bed')->nullable()->after('model_id');
            $table->unsignedBigInteger('bath')->nullable()->after('bed');
            $table->unsignedBigInteger('square_feet')->nullable()->after('bath');
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
            if (Schema::hasColumn('bed', 'bath', 'square_feet')) {
                $table->dropColumn(['bed', 'bath', 'square_feet']);
            }
        });
    }
}
