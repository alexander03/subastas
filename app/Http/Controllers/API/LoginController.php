<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Acceso;
use App\Http\Resources\TipousuarioResource;
use App\Http\Resources\DistritoResource;
use App\Http\Resources\AccesoResource;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $this->validateLogin($request);
        if(Auth::attempt($request->only('email','password'))){
            //$data = Auth::login($request->user(), true);
            $user = $request->user();
            $user->tipousuario = new TipousuarioResource($user->tipousuario);
            $accesos = Acceso::getByTipousuario($user->tipousuario_id)->get();
            $lista = AccesoResource::collection($accesos);
            $user->distrito = new DistritoResource($user->distrito);
            $user->accesos = $lista;
            return response()->json([
                'status' => true,
                'token' => $request->user()->createToken($request->email)->plainTextToken,
                'message' => 'Success',
                'usuario' => $user,
            ]);
        }
        
        return response()->json('Unauthenticated');
    }

    public function validateLogin(Request $request)
    {
        return $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json('Sesion cerrada correctamente');
    }

    public function authenticate(Request $request){
        $user = $request->user();
        $user->tipousuario = new TipousuarioResource($user->tipousuario);
        $accesos = Acceso::getByTipousuario($user->tipousuario_id)->get();
        $lista = AccesoResource::collection($accesos);
        $user->accesos = $lista;
        $user->distrito = new DistritoResource($user->distrito);
        return response()->json($user);
    }

}
