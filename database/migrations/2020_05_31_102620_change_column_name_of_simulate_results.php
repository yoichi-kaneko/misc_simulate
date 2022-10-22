<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnNameOfSimulateResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('simulate_results', function (Blueprint $table) {
            $table->renameColumn('simulate_result_id', 'simulate_param_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('simulate_results', function (Blueprint $table) {
            $table->renameColumn('simulate_param_id', 'simulate_result_id');
        });
    }
}
