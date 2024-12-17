<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePerformerIdToAwayTeamInEventsTable extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->renameColumn('performer_id', 'away_team');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->renameColumn('away_team', 'performer_id');
        });
    }
}
