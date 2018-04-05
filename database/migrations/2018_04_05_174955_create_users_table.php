<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('openid',100)->nullable()->comment('微信openid');
            $table->string('nickname',50)->nullable()->comment('用户昵称');
            $table->string('phone',20)->nullable()->comment('手机号');
            $table->unsignedTinyInteger('age')->nullable()->comment('年龄');
            $table->string('sex',1)->nullable()->comment('性别');
            $table->string('province',50)->nullable()->comment('省份');
            $table->string('city',50)->nullable()->comment('城市');
            $table->string('country',50)->nullable()->comment('国家');
            $table->string('avatar',1000)->nullable()->comment('头像url');
            $table->unique('openid');
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
        Schema::dropIfExists('users');
    }
}
