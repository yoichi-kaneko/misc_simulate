<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSimulateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('simulate_results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('simulate_id')->index();
            $table->bigInteger('simulate_result_id')->index();
            $table->integer('rank')->comment('表示の順序');
            $table->string('x_axis_label', 32)->comment('x軸の表示ラベル');
            $table->string('result')->comment('結果。json形式');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('simulate_results');
    }
}
