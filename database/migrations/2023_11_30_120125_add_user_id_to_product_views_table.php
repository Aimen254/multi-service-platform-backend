<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToProductViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_views', function (Blueprint $table) {
            $table->unsignedBigInteger('module_id')->nullable()->after('ip_address');
            $table->unsignedBigInteger('user_id')->nullable()->after('module_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_views', function (Blueprint $table) {
            $table->dropColumn('module_id');
            $table->dropColumn('user_id');
        });
    }
}
