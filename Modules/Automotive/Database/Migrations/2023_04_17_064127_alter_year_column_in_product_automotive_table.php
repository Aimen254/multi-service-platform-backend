<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterYearColumnInProductAutomotiveTable extends Migration
{
    public function up()
    {
        Schema::table('product_automotives', function (Blueprint $table) {
            $table->string('year')->change();
        });
    }

    public function down()
    {
        Schema::table('product_automotives', function (Blueprint $table) {
            $table->date('year')->change();
        });
    }
}
