<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToVehicleReviewsTable extends Migration
{

    public function up()
    {
        // Check if the column already exists before adding it
        if (!Schema::hasColumn('vehicle_reviews', 'user_id')) {
            Schema::table('vehicle_reviews', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->foreign('user_id')->references('id')->on('users');
            });
        }
    }
    public function down()
    {
        Schema::table('vehicle_reviews', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
}
