<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDiscussion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discussion', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',127)->default('')->comment('标题');
            $table->string('content',127)->default('')->comment('话题内容');
            $table->text('pics')->comment('图片地址以逗号分隔');
            $table->integer('user_id')->comment('发起人id');
            $table->date('add_time')->comment('发起时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discussion');
    }
}
