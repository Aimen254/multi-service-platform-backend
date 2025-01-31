<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTypeOfColumnInProductAutomotive extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_automotives', function (Blueprint $table) {
            $table->string('transmission')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_automotives', function (Blueprint $table) {
            $table->enum('transmission', ['manual', 'automatic'])->default('automatic')->change();
        });
    }
}
