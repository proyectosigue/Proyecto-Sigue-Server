<?php

namespace App\Http\Controllers;


use Hash;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\APIResponse;

class UserController extends Controller
{

    public function index()
    {
        $users = User::all()->active()->toArray();
        return response()->json($users);
    }

    public function show($user)
    {
        try {
            return response()->json(['user' => User::findOrFail($user)]);
        } catch (Exception $e) {
            return response()->json(['status' => 'Error', 'messages' =>
                ['Ocurrió un error al obtener'],
                ['debug' => $e->getMessage()]]);
        }
    }

    public function updateFCMToken(User $user, Request $request){
        $user = User::find($user->id);
        $user->fcm_token = $request->fcm_token;
        $user->save();
        return response()->json(APIResponse::success('Token FCM Actualizado'));
    }

}