<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSocialLinksColumnsToBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('facebook_id')->after('url')->nullable();
            $table->string('instagram_id')->after('url')->nullable();
            $table->string('twitter_id')->after('url')->nullable();
            $table->string('pinterest_id')->after('url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['facebook_id', 'instagram_id', 'twitter_id', 'pinterest_id']);
        });
    }
}
