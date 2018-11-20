<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFound extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('found', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',32)->default('')->comment('基金名称');
            $table->integer('account')->default(0)->comment('基金募集金额');
            $table->string('unit',32)->default('USDT')->comment('货币单位');
            $table->integer('user_id')->default(0)->comment('用户id');
            $table->dateTime('add_time')->default(null)->comment('创建时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('found');
    }
}
