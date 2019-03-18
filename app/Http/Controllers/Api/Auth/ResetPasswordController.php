<?php

namespace App\Http\Controllers\Api\Auth;

use DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\User;
use Hash;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{

    public function resetPasswordForm(Request $request, $token)
    {
        $valid = DB::table('password_resets')->where('token', $token)->first();

        if ($valid) {
            return response()->view('reset_password.reset_password', [
                'token' => $token,
            ]);
        } else {
            return "Acceso Denegado :(";
        }
    }

    public function resetPassword(ChangePasswordRequest $request)
    {
        $validFields = DB::table('password_resets')->where('email', $request->email)
                                                   ->where('token', $request->token)
                                                   ->first();

        if ($validFields) {
            $user = User::whereEmail($request->email)->update([
                'password' => Hash::make($request->password),
            ]);

            DB::table('password_resets')->where('email', $request->email)
                                        ->where('token', $request->token)
                                        ->delete();

            return response()->view('reset_password.success');
        }

        return redirect()->back()->withErrors([
            'email' => 'El email ingresado no es correcto',
        ]);
    }
}
