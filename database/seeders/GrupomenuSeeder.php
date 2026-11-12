<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupomenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('accesos')->delete();
        DB::table('grupomenus')->insert(array(
            array(
                'nombre' => 'Configuracion',
                'icono' => '',
                'orden' => '1',
                'created_at' => '2023-08-16 00:00:00',
                'updated_at' => '2023-08-16 00:00:00',
            )
        ));
    }
}
