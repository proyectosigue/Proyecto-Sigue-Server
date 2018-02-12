<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionsRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("permissions_roles", function (Blueprint $table) {
            $table->increments("id");
            $table->integer("role_id")->unsigned();
            $table->integer("permission_id")->unsigned();
            $table->foreign("role_id")->references("id")->on("roles");
            $table->foreign("permission_id")->references("id")->on("permissions");
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
        Schema::dropIfExists('permissions_roles');
    }
}
