<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLab extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lab', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->default(0)->index('parent_id')->comment('父级id，默认0');
            $table->string('lab_name',64)->default('')->index('lab_name')->comment('标签名');
            $table->smallInteger('is_delete')->default(0)->comment('0未删除，1已删除');
            $table->integer('user_id')->default(0)->comment('创建者id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lab');
    }
}
