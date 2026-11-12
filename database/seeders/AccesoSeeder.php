<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccesoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('accesos')->delete();
        DB::table('accesos')->insert(array(
            array(
                'opcionmenu_id' => '1',
                'tipousuario_id' => '1',
                'created_at' => '2023-08-16 00:00:00',
                'updated_at' => '2023-08-16 00:00:00',
            ),
            array(
                'opcionmenu_id' => '2',
                'tipousuario_id' => '1',
                'created_at' => '2023-08-16 00:00:00',
                'updated_at' => '2023-08-16 00:00:00',
            ),
            array(
                'opcionmenu_id' => '3',
                'tipousuario_id' => '1',
                'created_at' => '2023-08-16 00:00:00',
                'updated_at' => '2023-08-16 00:00:00',
            ),
            array(
                'opcionmenu_id' => '4',
                'tipousuario_id' => '1',
                'created_at' => '2023-08-16 00:00:00',
                'updated_at' => '2023-08-16 00:00:00',
            )
        ));
    }
}
