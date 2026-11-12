<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBiensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('biens', function (Blueprint $table) {
            $table->id();
            $table->string('situacion','1');
            $table->decimal('valorreferencia',18,2);
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('proceso_id');
            $table->foreign('producto_id', 'Producto_id_foreign')->references('id')->on('productos');
            $table->foreign('proceso_id', 'Proceso_id_foreign')->references('id')->on('procesos');
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
        Schema::dropIfExists('biens');
    }
}
