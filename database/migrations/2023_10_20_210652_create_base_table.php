<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bases', function (Blueprint $table) {
            $table->id();
            $table->string('situacion',1);
            $table->string("comentario")->nullable();
            $table->unsignedBigInteger('proceso_id');
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('proceso_id', 'Bases_Proceso_id_foreign')->references('id')->on('procesos');
            $table->foreign('usuario_id', 'Bases_Usuario_id_foreign')->references('id')->on('users');
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
        Schema::dropIfExists('bases');
    }
}
