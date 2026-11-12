<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtapasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etapas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo',1);
            $table->dateTime('fechainicio');
            $table->dateTime('fechafin');
            $table->string('situacion',1);
            $table->unsignedBigInteger('etapa_id')->nullable();
            $table->unsignedBigInteger('proceso_id');
            $table->foreign('etapa_id', 'Etapa_Etapa_id_foreign')->references('id')->on('etapas');
            $table->foreign('proceso_id', 'Etapa_Proceso_id_foreign')->references('id')->on('procesos');
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
        Schema::dropIfExists('etapas');
    }
}
