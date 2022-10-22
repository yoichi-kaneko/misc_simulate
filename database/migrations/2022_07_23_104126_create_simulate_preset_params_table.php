<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSimulatePresetParamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('simulate_preset_params', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->comment('シミュレーション種別');
            $table->string('title', 32)->comment('タイトル');
            $table->text('params')->comment('パラメータ');
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
        Schema::dropIfExists('simulate_preset_params');
    }
}
