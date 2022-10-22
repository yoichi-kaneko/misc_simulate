<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSimulateParamNames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('simulate_params', function (Blueprint $table) {
            $table->renameColumn('challenge_fee', 'participation_fee');
            $table->renameColumn('banker_cache', 'banker_prepared_change');
            $table->renameColumn('maximum_challenge_time', 'banker_budget_degree');
        });

        Schema::table('simulate_queues', function (Blueprint $table) {
            $table->renameColumn('challenge_fee', 'participation_fee');
            $table->renameColumn('banker_cache', 'banker_prepared_change');
            $table->renameColumn('maximum_challenge_time', 'banker_budget_degree');
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
            $table->renameColumn('participation_fee', 'challenge_fee');
            $table->renameColumn('banker_prepared_change', 'banker_cache');
            $table->renameColumn('banker_budget_degree', 'maximum_challenge_time');
        });

        Schema::table('simulate_queues', function (Blueprint $table) {
            $table->renameColumn('participation_fee', 'challenge_fee');
            $table->renameColumn('banker_prepared_change', 'banker_cache');
            $table->renameColumn('banker_budget_degree', 'maximum_challenge_time');
        });
    }
}
