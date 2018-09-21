<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ThreadsUsersOnDeleteCascade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('threads', function(Blueprint $table){
            $table->dropForeign(['user_id_issuing']);
            $table->dropForeign(['user_id_receiver']);

            $table->foreign('user_id_issuing')->references('id')->on('users')
            ->onDelete('cascade');

            $table->foreign('user_id_receiver')->references('id')->on('users')
            ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('threads', function(Blueprint $table){
            $table->dropForeign(['user_id_issuing']);
            $table->dropForeign(['user_id_receiver']);

            $table->foreign('user_id_issuing')->references('id')->on('users');
            $table->foreign('user_id_receiver')->references('id')->on('users');

        });
    }
}
