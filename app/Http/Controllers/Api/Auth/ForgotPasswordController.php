<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function forgottenPassword(Request $request)
    {
        if (!$this->validateEmail($request->email)) {
            return response()->json([
                'status' => 'Error',
                'message' => 'El email proporcionado no fue encontrado',
            ]);
        }

        $this->sendResetPasswordEmail($request->email);

        return response()->json([
            'status' => 'Exito',
            'message' => 'Por favor verifique su email para poder reiniciar su password',
        ]);
    }

    protected function validateEmail($email)
    {
        return !!User::where('email', $email)->first();
    }

    protected function sendResetPasswordEmail($email)
    {
        $token = $this->createToken($email);
        Mail::to($email)->send(new ResetPasswordMail($token));
    }

    protected function createToken($email)
    {
        $oldToken = DB::table('password_resets')->where('email', $email)->first();
        if ($oldToken) {
            return $oldToken->token;
        }

        $token = str_random(60);
        $this->saveToken($token, $email);

        return $token;
    }

    protected function saveToken($token, $email)
    {
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
    }
}
