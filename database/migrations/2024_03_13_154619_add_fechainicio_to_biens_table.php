<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechainicioToBiensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->timestamp('fechainicio')->nullable()->after('proceso_id');
            $table->timestamp('fechafin')->nullable()->after('proceso_id');
            $table->integer('bid_id')->nullable()->after('proceso_id');
            $table->integer('segundoactual')->nullable()->after('bid_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('biens', function (Blueprint $table) {
            //
        });
    }
}
