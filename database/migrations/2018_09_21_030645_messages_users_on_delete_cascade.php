<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MessagesUsersOnDeleteCascade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function(Blueprint $table){
            $table->dropForeign(['thread_id']);
            $table->dropForeign(['user_id_replier']);

            $table->foreign('thread_id')->references('id')->on('threads')
            ->onDelete('cascade');
            $table->foreign('user_id_replier')->references('id')->on('users')
            ->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function(Blueprint $table){
            $table->dropForeign(['thread_id']);
            $table->dropForeign(['user_id_replier']);

            $table->foreign('thread_id')->references('id')->on('threads');
            $table->foreign('user_id_replier')->references('id')->on('users');
        });
    }
}
