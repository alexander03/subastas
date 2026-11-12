<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInscripcionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inscripcions', function (Blueprint $table) {
            $table->id();
            $table->dateTime("fecha");
            $table->decimal("monto")->nullable();
            $table->string("situacion",1);
            $table->string("facturacion",20);
            $table->string("comentario")->nullable();
            $table->unsignedBigInteger('etapa_id');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('bien_id');
            $table->foreign('etapa_id', 'Inscripcion_Etapa_id_foreign')->references('id')->on('etapas');
            $table->foreign('usuario_id', 'Inscripcion_Usuario_id_foreign')->references('id')->on('users');
            $table->foreign('bien_id', 'Inscripcion_Bien_id_foreign')->references('id')->on('biens');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inscripcions');
    }
}
