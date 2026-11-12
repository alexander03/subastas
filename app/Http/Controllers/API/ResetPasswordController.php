<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\ResetCodePassword;
use App\Models\User;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    public function __invoke(ResetPasswordRequest $request)
    {
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

        if ($passwordReset->isExpire()) {
            return $this->jsonResponse(null, 'Código ha expirado', 422);
        }

        $user = User::firstWhere('email', $passwordReset->email);

        $user->update($request->only('password'));

        ResetCodePassword::where('email', $request->email)->delete();
        //$passwordReset->delete();

        return response(['Password fue actualizado correctamente'], 200);
    }
}
