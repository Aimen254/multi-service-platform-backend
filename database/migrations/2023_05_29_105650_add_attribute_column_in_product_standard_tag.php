<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttributeColumnInProductStandardTag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_standard_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('attribute_id')->nullable();
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
            Schema::dropColumns('attribute_id');
        });
    }
}
