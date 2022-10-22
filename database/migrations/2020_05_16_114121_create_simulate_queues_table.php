<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSimulateQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('simulate_queues', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->comment('キューのステータス');
            $table->bigInteger('simulate_id');
            $table->integer('player_number')->comment('挑戦者の総数');
            $table->integer('banker_cache')->comment('胴元の運転資金');
            $table->integer('challenge_fee')->comment('挑戦者の参加手数料');
            $table->integer('maximum_challenge_time')->comment('最大の挑戦回数');
            $table->integer('random_seed')->comment('ランダムシード')->nullable();
            $table->integer('iteration')->comment('繰り返し回数');
            $table->string('x_axis_label', 32)->comment('x軸の表示ラベル');
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
        Schema::dropIfExists('simulate_queues');
    }
}
