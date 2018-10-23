<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatTableRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relationship', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',64)->comment('名字');
            $table->string('phone',32)->comment('手机号');
            $table->string('wechat',127)->default('')->comment('微信');
            $table->string('email',127)->comment('邮箱');
            $table->string('avatar_url',255)->comment('头像url地址');
            $table->string('company',64)->comment('所在公司');
            $table->string('position',64)->comment('岗位');
            $table->string('rank',64)->comment('职级');
            $table->smallInteger('industry_id')->index('industry_id')->comment('行业id');
            $table->text('note')->comment('备注');
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
        Schema::dropIfExists('relationship');
    }
}
