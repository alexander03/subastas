<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccesosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accesos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('opcionmenu_id');
            $table->unsignedBigInteger('tipousuario_id');
            $table->foreign('opcionmenu_id', 'Opcionmenu_id_foreign')->references('id')->on('opcionmenus');
            $table->foreign('tipousuario_id', 'Tipousuario_id_foreign')->references('id')->on('tipousuarios');
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
        Schema::dropIfExists('accesos');
    }
}
