<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHierarchyIdToProductStandardTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_standard_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('hierarchy_id')->after('attribute_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_standard_tag', function (Blueprint $table) {
            $table->dropColumn('hierarchy_id');
        });
    }
}
