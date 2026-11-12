<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OpcionmenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('accesos')->delete();
        DB::table('opcionmenus')->insert(array(
            array(
                'nombre' => 'Grupo Menu',
                'ruta' => 'grupomenu',
                'icono' => '',
                'orden' => '1',
                'grupomenu_id' => '1',
                'created_at' => '2023-08-16 00:00:00',
                'updated_at' => '2023-08-16 00:00:00',
            ),
            array(
                'nombre' => 'Opcion Menu',
                'ruta' => 'opcionmenu',
                'icono' => '',
                'orden' => '2',
                'grupomenu_id' => '1',
                'created_at' => '2023-08-16 00:00:00',
                'updated_at' => '2023-08-16 00:00:00',
            ),
            array(
                'nombre' => 'Tipo Usuario',
                'ruta' => 'tipousuario',
                'icono' => '',
                'orden' => '3',
                'grupomenu_id' => '1',
                'created_at' => '2023-08-16 00:00:00',
                'updated_at' => '2023-08-16 00:00:00',
            ),
            array(
                'nombre' => 'Usuarios',
                'ruta' => 'usuario',
                'icono' => '',
                'orden' => '4',
                'grupomenu_id' => '1',
                'created_at' => '2023-08-16 00:00:00',
                'updated_at' => '2023-08-16 00:00:00',
            )
        ));
    }
}
