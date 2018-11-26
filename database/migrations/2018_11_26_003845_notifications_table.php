<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create("notifications", function(Blueprint $table){
        $table->increments('id');
        $table->integer('thread_id')->unsigned();
        $table->integer('message_id')->unsigned();
        $table->integer('user_id')->unsigned();
        $table->foreign("thread_id")->references("id")->on("threads");
        $table->foreign("message_id")->references("id")->on("messages");
        $table->foreign("user_id")->references("id")->on("users");
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::dropIfExists('notifications');
    }
}
