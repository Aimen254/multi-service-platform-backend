<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RearrangeColumnsInWishlistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // rename the columns that wanted to 're-position'
        Schema::table('wishlists', function (Blueprint $table) {
            if (Schema::hasColumns('wishlists', ['model_id', 'model_type'])) {
                $table->renameColumn('model_id', 'table_id')->change();
                $table->renameColumn('model_type', 'table_type')->change();
            }
        });

        // create new columns with 'accurate position'
        Schema::table('wishlists', function (Blueprint $table) {
            if (!Schema::hasColumns('wishlists', ['model_id', 'model_type'])) {
                $table->unsignedBigInteger('model_id')->after('module_id');
                $table->string('model_type')->after('model_id');
            }
        });

        // now assigning the data from 'renamed columns' to 'accurated positioned columns'
        if (Schema::hasColumns('wishlists', ['model_id', 'model_type']) && Schema::hasColumns('wishlists', ['table_id', 'table_type'])) {
            DB::table('wishlists')->update([
                'model_id' => DB::raw('table_id'),
                'model_type' => DB::raw('table_type')
            ]);
        }

        // now dropping the 'renamed columns'
        Schema::table('wishlists', function (Blueprint $table) {
            if (Schema::hasColumns('wishlists', ['table_id', 'table_type'])) {
                $table->dropColumn('table_id');
                $table->dropColumn('table_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wishlists', function (Blueprint $table) {
            //
        });
    }
}
