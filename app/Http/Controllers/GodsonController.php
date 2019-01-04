<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use App\Godson;
use Spatie\Dropbox\Client;
use App\Http\Requests\GodsonRequest;
use Illuminate\Support\Facades\Storage;

class GodsonController extends Controller
{
    public function index()
    {
        $godsons = Godson::orderBy('id', 'asc')->where('status', 1)
            ->with('godfathers')->get();
        return response()->json($godsons);
    }

    public function show(Godson $godson)
    {
        try {
            return response()->json(['godson' => $godson]);
        } catch (Exception $e) {
            return response()->json(['status' => 'Error', 'messages' =>
                ['Ocurrió un error al obtener'],
                ['debug' => $e->getMessage()]]);
        }
    }

    public function store(GodsonRequest $request)
    {
        try {
            if (isset($request->profile_image)) {
              $file_date_title = date('Y_m_d_H_i_s').'.jpeg';
              $photography_url = "profile-images-godsons/$file_date_title";

              $dropbox = Storage::disk('dropbox')->getDriver()->getAdapter()->getClient();
              Storage::disk('dropbox')->put($photography_url, base64_decode($request->profile_image));
              $shared_response = $dropbox->createSharedLinkWithSettings($photography_url, ["requested_visibility" => "public"]);
              $photography_url = str_replace("?dl=0", "?dl=1", $shared_response['url']);

            }
            else {
                $photography_url = "";
            }

            $godson = Godson::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'profile_image' => $photography_url,
                'age' => $request->input('age'),
                'orphan_house_id' => $request->input('orphan_house_id')
            ]);

            $godson->godfathers()->attach($request->godfather_id);

            return response()->json(['status' => 'Éxito', 'messages' => ['Se ha registrado al usuario como Ahijado']]);
        } catch (Exception $e) {
            return response()->json(['status' => 'Error', 'messages' =>
                ['Ocurrió un error en el registro'],
                ['debug' => $e->getMessage(). ' on line '.$e->getLine()]]);
        }
    }

    public function update(GodsonRequest $request, Godson $godson){

        try {
            if($request->profile_image) {

                if ($godson->profile_image) {
                    $cloud_link = $godson->getOriginal('profile_image');
                    $cloud_link = preg_replace('/(.*\/.\/*\/)\w+\//', '', $cloud_link);
                    $cloud_link = str_replace("?dl=1", '', $cloud_link);
                    Storage::disk('dropbox')->delete('profile-images-godsons/'.$cloud_link);
                }

                $photo_name = date('Y_m_d_H_i_s').'.jpeg';
                $full_file_address = 'profile-images-godsons/'.$photo_name;

                $dropbox = Storage::disk('dropbox')->getDriver()->getAdapter()->getClient();
                Storage::disk('dropbox')->put($full_file_address, base64_decode($request->profile_image));
                $shared_response = $dropbox->createSharedLinkWithSettings($full_file_address, ["requested_visibility" => "public"]);
                $photography_url = str_replace("?dl=0", "?dl=1", $shared_response['url']);

            } else if($godson->profile_image){
                $photography_url = $godson->profile_image;
            } else {
                $photography_url = '';
            }

            $godson->update([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'profile_image' => $photography_url,
                'age' => $request->input('age'),
                'orphan_house_id' => $request->input('orphan_house_id')
            ]);


            $godson->godfathers()->detach($godson->godfather_id);
            $godson->godfathers()->attach($request->godfather_id);

            return response()->json(['status' => 'Éxito', 'messages' => ['Se ha actualizado la información del ahijado']]);
        } catch (Exception $e) {
            return response()->json(['status' => 'Error', 'messages' =>
                ['Ocurrió un error al actualizar al ahijado'],
                ['debug' => $e->getMessage(). ' on line '.$e->getLine()]]);
        }
    }

    public function destroy(Godson $godson)
    {
        try {
            $godson->godfathers()->detach();
            $godson->delete();
            return response()->json(['status' => 'Éxito', 'messages' => ['Se ha borrado el ahijado']]);
        } catch (Exception $e) {
            return response()->json(['status' => 'Error', 'messages' =>
                ['Ocurrió un error al borrar'],
                ['debug' => $e->getMessage() . ' on line ' . $e->getLine()]]);
        }
    }

    public function getGodfathers(Godson $godson){
        try {
            return response()->json(['status' => 'Success', 'data' => $godson->godfathers]);
        } catch (Exception $e) {
            return response()->json(['status' => 'Error', 'messages' =>
                ['Ocurrió un error al borrar'],
                ['debug' => $e->getMessage() . ' on line ' . $e->getLine()]]);
        }
    }
}
