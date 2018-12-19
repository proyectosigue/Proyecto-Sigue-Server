<?php

namespace App\Http\Controllers;

use Hash;
use App\User;
use App\Role;
use Exception;
use App\Godson;
use App\Thread;
use Spatie\Dropbox\Client;
use Illuminate\Http\Request;
use App\Http\Requests\GodfatherRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\GodfatherEditRequest;

class GodfatherController extends Controller
{
    public function index()
    {
        return response()->json(User::godfathers()->active()->get());
    }

    public function show(User $user)
    {
        try {
            return response()->json(['godfather' => $user]);
        } catch (Exception $e) {
            return response()->json(['status' => 'Error', 'messages' =>
                ['Ocurrió un error al obtener'],
                ['debug' => $e->getMessage() . ' on line ' . $e->getLine()]]);
        }
    }

    public function store(GodfatherRequest $request)
    {
        if (count(User::where('email', $request->input('email'))->get()) > 0) {
            return response()->json(['status' => 'Error', 'messages' => ['El email ya está dado de alta']]);
        }

        try {

            $user = User::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'interests' => $request->input('interests'),
                'phone' => $request->input('phone'),
                'password' => Hash::make($request->input('password')),
                'email' => $request->input('email'),
                'profile_image' => ''
            ]);
            $user->roles()->attach(Role::where('description', 'Padrino')->first()->id);

            return response()->json([
                'header' => 'Éxito',
                'status' => 'success',
                'messages' => ['Se ha registrado al usuario como Padrino'],
                'data' => [
                    'id' => $user->id
                ]
            ]);

        } catch (Exception $e) {
            return response()->json(['header' => 'Error', 'status' => 'error', 'messages' =>
                ['Ocurrió un error en el registro'],
                ['debug' => $e->getMessage() . ' on line ' . $e->getLine()]]);
        }
    }

    public function update(GodfatherEditRequest $request, $godfather)
    {

        try {

            $godfather = User::find($godfather);
            $godfather->first_name = $request->first_name;
            $godfather->last_name = $request->last_name;
            $godfather->phone = $request->phone;
            $godfather->interests = isset($request->interests) ? $request->interests : null;
            $godfather->email = $request->email;

            if($request->password){
                $godfather->password = Hash::make($request->password);
            }

            $godfather->save();

            return response()->json([
                'header' => 'Éxito',
                'status' => 'success',
                'messages' => ['Se ha actualizado la información del padrino'],
                'data' => $godfather
            ]);

        } catch (Exception $e) {
            return response()->json(['status' => 'Error', 'messages' =>
                ['Ocurrió un error al actualizar'],
                ['debug' => $e->getMessage() . ' on line ' . $e->getLine()]]);
        }
    }

    public function destroy(User $user)
    {
        try {

            $user->status = 0;
            $user->save();

            return response()->json(['status' => 'Éxito', 'messages' => ['Se ha desactivado el padrino']]);
        } catch (Exception $e) {
            return response()->json(['status' => 'Error', 'messages' =>
                ['Ocurrió un error al borrar'],
                ['debug' => $e->getMessage() . ' on line ' . $e->getLine()]]);
        }
    }

    public function uploadProfileImage(Request $request, User $user)
    {
        try {
            if($user->profile_image !== null && $user->profile_image !== ""){
                $cloud_link = $user->getOriginal('profile_image');
                $cloud_link = preg_replace('/(.*\/.\/*\/)\w+\//', '', $cloud_link);
                $cloud_link = str_replace("?dl=1", '', $cloud_link);
                Storage::disk('dropbox')->delete('profile-images-godfathers/'.$cloud_link);
            }

            $file_date_title =  date('Y_m_d_H_i_s').'.jpeg';
            $full_file_address = "profile-images-godfathers/$file_date_title";

            $dropbox = Storage::disk('dropbox')->getDriver()->getAdapter()->getClient();
            Storage::disk('dropbox')->put($full_file_address, base64_decode($request->profile_image['value']));

            $shared_response = $dropbox->createSharedLinkWithSettings(
                            $full_file_address,
                            ["requested_visibility" => "public"]
                        );
            $shared_url = str_replace("?dl=0", "?dl=1", $shared_response['url']);

            $user->profile_image = $shared_url;
            $user->save();

            return response()->json([
                'header' => 'Éxito',
                'status' => 'success',
                'messages' => ['Se ha colocado la foto de perfil']
            ]);
        } catch (Exception $e) {
            return response()->json([
                'header' => 'Error',
                'status' => 'error',
                'messages' => ['Ocurrió un error, contacte al soporte'],
                ['debug' => $e->getMessage() . ' on line ' . $e->getLine()]]);
        }
    }

    public function toggleGodson(User $user, Godson $godson)
    {
        try {

            $user->godsons()->toggle($godson->id);

            if ($user->godsons()->where('id', $godson->id)->first() != null) {
                return response()->json(['status' => 'Éxito', 'message' => 'El padrino ha comenzado a apadrinar al ahijado']);
            }

            return response()->json(['status' => 'Éxito', 'message' => 'El padrino ha dejado de apadrinar al ahijado']);

        } catch (Exception $e) {
            return response()->json(['status' => 'Error', 'messages' =>
                ['Ocurrió un error al borrar'],
                ['debug' => $e->getMessage() . ' on line ' . $e->getLine()]]);
        }
    }

    public function getGodsons(User $user)
    {
        try {
            return response()->json(['status' => 'Success', 'data' => $user->godsons->load('godfathers')]);
        } catch (Exception $e) {
            return response()->json(['status' => 'Error', 'messages' =>
                ['Ocurrió un error al borrar'],
                ['debug' => $e->getMessage() . ' on line ' . $e->getLine()]]);
        }
    }

}
