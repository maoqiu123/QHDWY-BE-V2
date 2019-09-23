<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTFgjTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_fgj', function (Blueprint $table) {
            $table->string('id');
            //房管局名称
            $table->string('smc');
            //行政区划代码
            $table->string('sxzqh');
            //房管局登记
            $table->string('jc');
            $table->string('login_name')->unique();
            $table->string('password');
            $table->tinyInteger('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_fgj');
    }
}
