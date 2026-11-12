<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bid;
use App\Models\Bien;
use App\Providers\BidPlaced;
use App\Providers\BidManual;
use App\Providers\Contador;
use Illuminate\Support\Facades\DB;

class BidController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'bien_id' => 'required',
        ]);
        $lista = Bid::where('bien_id', $data['bien_id'])->with('user')->get();
        return response()->json($lista);
    }

    public function store(Request $request)
    {
        $bien = Bien::find($request->input('bien_id'));
        if($bien->situacion=='E'){//inicio subasta del bien
            $data = $request->validate([
                'user_id' => 'required',
                'bien_id' => 'required',
                'amount' => 'required',
            ]);
            $bid = Bid::create($data);
            //guardo la ultima puja al bien
            $bien->bid_id = $bid->id;
            if($bien->proceso->modalidad=="A Viva Voz"){
                $bien->segundoactual = null;
                $bien->save();
                BidManual::dispatch($bid);
            }else{
                $bien->segundoactual = $bien->proceso->tiempo;
                $bien->save();

                BidPlaced::dispatch($bid);

                $mensaje = ["contador"=>$bien->segundoactual,"mensaje"=>"Conteo subasta"];
                Contador::dispatch($bien,$mensaje); 
            }
            return response()->json($bid);
        }elseif($bien->situacion=='P'){
            return response()->json("Subasta aun no empieza");
        }elseif($bien->situacion=='F'){
            return response()->json("Subasta finalizada");
        }
    }
}
