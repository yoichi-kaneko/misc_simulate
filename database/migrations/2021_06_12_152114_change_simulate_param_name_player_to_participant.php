<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSimulateParamNamePlayerToParticipant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('simulate_params', function (Blueprint $table) {
            $table->renameColumn('player_number', 'participant_number');
        });

        Schema::table('simulate_queues', function (Blueprint $table) {
            $table->renameColumn('player_number', 'participant_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('simulate_params', function (Blueprint $table) {
            $table->renameColumn('participant_number', 'player_number');
        });

        Schema::table('simulate_queues', function (Blueprint $table) {
            $table->renameColumn('participant_number', 'player_number');
        });
    }
}
