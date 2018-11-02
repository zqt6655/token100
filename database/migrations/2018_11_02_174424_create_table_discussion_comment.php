<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDiscussionComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discussion_comment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('discussion_id')->comment('讨论话题id');
            $table->string('comment')->default('')->comment('评论内容');
            $table->integer('user_id')->comment('评论者id');
            $table->date('comment_time')->comment('评论时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discussion_comment');
    }
}
