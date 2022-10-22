<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSimulateTmpResultDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('simulate_tmp_result_details', function (Blueprint $table) {
            DB::statement('ALTER TABLE simulate_tmp_result_details MODIFY transitions MEDIUMBLOB');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('simulate_tmp_result_details', function (Blueprint $table) {
            $table->text('transitions')->comment('ゲームの金額の推移')->change();
        });
    }
}
