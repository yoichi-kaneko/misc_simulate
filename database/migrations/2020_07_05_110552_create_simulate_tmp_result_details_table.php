<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSimulateTmpResultDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('simulate_tmp_result_details', function (Blueprint $table) {
            $table->id();
            $table->integer('obtained_cache')->comment('ゲーム終了時の収支金額');
            $table->text('transitions')->comment('ゲームの金額の推移');
            $table->timestamps();
            $table->index(['obtained_cache']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('simulate_tmp_result_details');
    }
}
