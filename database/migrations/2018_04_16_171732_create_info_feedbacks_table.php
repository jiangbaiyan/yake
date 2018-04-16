<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfoFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('info_feedbacks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('info_id')->nullable()->comment('通知id');
            $table->integer('user_id')->nullable()->comment('通知对象id');
            $table->tinyInteger('status')->nullable()->default(0)->comment('是否阅读 0-未阅读 1-已阅读');
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
        Schema::dropIfExists('info_feedbacks');
    }
}
