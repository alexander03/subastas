<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpcionmenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opcionmenus', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('ruta');
            $table->string('orden');
            $table->string('icono');
            $table->unsignedBigInteger('grupomenu_id');
            $table->foreign('grupomenu_id', 'Grupomenu_id_foreign')->references('id')->on('grupomenus');
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
        Schema::dropIfExists('opcionmenus');
    }
}
