<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSimulatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('simulates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 32)->comment('タイトル');
            $table->string('header_label', 32)->comment('グラフ上部の表示ラベル');
            $table->string('x_axis_label', 32)->comment('x軸の表示ラベル');
            $table->integer('mode')->comment('保存するデータのモード');
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
        Schema::dropIfExists('simulates');
    }
}
