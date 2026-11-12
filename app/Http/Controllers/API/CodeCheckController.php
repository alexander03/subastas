<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CodeCheckRequest;
use App\Models\ResetCodePassword;
use Illuminate\Http\Request;

class CodeCheckController extends Controller
{
    public function __invoke(CodeCheckRequest $request)
    {
        $request->validate([
            'code' => 'required|string|exists:reset_code_passwords',
        ]);

        // find the code
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

        // check if it does not expired: the time is one hour
        if ($passwordReset->created_at > now()->addHour()) {
            $passwordReset->delete();
            return response(['message' => 'Código ya expiró'], 422);
        }

        return response([
            'code' => $passwordReset->code,
            'message' => 'Código correcto'
        ], 200);
    }
}
