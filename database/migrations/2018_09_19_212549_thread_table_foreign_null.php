<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ThreadTableForeignNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('threads', function(Blueprint $table){
           $table->unsignedInteger('user_id_issuing')->nullable()->change();
           $table->unsignedInteger('user_id_receiver')->nullable()->change();
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
            $table->unsignedInteger('user_id_issuing')->change();
            $table->unsignedInteger('user_id_receiver')->change();
        });
    }
}
