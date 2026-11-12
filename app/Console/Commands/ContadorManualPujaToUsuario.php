<?php

namespace App\Console\Commands;

use App\Models\Bien;
use App\Providers\Subasta;
use Illuminate\Console\Command;

class ContadorManualPujaToUsuario extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proceso:contadormanual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar Contador Manual de ultima puja a los usuarios';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $listaBien = Bien::join('procesos','procesos.id','=','biens.proceso_id')
                        ->whereNull('procesos.deleted_at')
                        ->whereIn('procesos.modalidad',['A Viva Voz'])
                        ->where('biens.situacion','like','E')
                        ->select('biens.*')->get();
        foreach($listaBien as $k=>$bien){
            if(!is_null($bien->bid_id) && $bien->bid_id>0 && !is_null($bien->segundoactual)){
                if($bien->segundoactual>0){
                    $bien->segundoactual = $bien->segundoactual - 1;
                    $bien->save();
                    $mensaje = ["contador"=>$bien->segundoactual,"mensaje"=>"Conteo subasta"];
                    Subasta::dispatch($bien,$mensaje);      
                }else{
                    $bien->situacion='F';
                    $bien->fechafin = date("Y-m-d H:i:s");
                    $bien->save();
                    $mensaje = ["contador"=>"-3","mensaje"=>"Finalizo subasta"];
                    Subasta::dispatch($bien,$mensaje);      
                }
            }
        }
    }
}
