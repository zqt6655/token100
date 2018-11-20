<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFoundProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('found_project', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('found_id')->comment('基金id');
            $table->integer('project_id')->comment('项目id');
            $table->smallInteger('op_type')->default(0)->comment('操作类型,0买入，1卖出');
            $table->decimal('price',10,5)->default(0)->comment('交易的价格');
            $table->integer('num')->default(0)->comment('交易的数量');
            $table->integer('user_id')->default(0)->comment('用户id');
            $table->dateTime('add_time')->comment('交易的时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('found_project');
    }
}
