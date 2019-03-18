<?php

namespace App\Http\Controllers;

use DB;
use App\Event;
use Spatie\Dropbox\Client;
use Illuminate\Http\Request;
use App\Http\Requests\EventRequest;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
      return response()->json(Event::orderBy('created_at', 'desc')->get());
    }

    public function store(EventRequest $request) {
      try {
        $eventInstance = Event::create([
          "title" => $request->input('title'),
          "description" => $request->input('description'),
          "created_by" => $request->input('created_by'),
        ]);

        if ($request->image['new_image']) {
          $file_date_title = 'event-images/'.date('Y_m_d_H_i_s').'.jpeg';

          $dropbox = Storage::disk('dropbox')->getDriver()->getAdapter()->getClient();
          Storage::disk('dropbox')->put($file_date_title, base64_decode($request->image['new_image']['value']));

            $response = $dropbox->createSharedLinkWithSettings(
                        $file_date_title,
                       ["requested_visibility" => "public"]
                   );
            $response_url = str_replace("?dl=0", "?dl=1", $response['url']);

          $eventInstance->image = $response_url;
          $eventInstance->save();

        }

        $response_to_send = [
            'header' => 'Éxito',
            'status' => 'success',
            'messages' => ['Se ha registrado la noticia'],
            'data' => [
                'id' => $eventInstance->id,
            ],
        ];

        if (isset($response)) {
            $response_to_send['data']['file'] = $response;
        }

        return response()->json($response_to_send);

      } catch (Exception $e) {
        return response()->json(['header' => 'Error', 'status' => 'error', 'messages' =>
            ['Ocurrió un error creando noticia'],
            ['debug' => $e->getMessage() . ' on line ' . $e->getLine()]]);
      }
    }

    public function update(EventRequest $request, Event $event)
    {
      try {
        $event->title = $request->input('title');
        $event->description = $request->input('description');

        if ($request->image) {

          if ($event->image) {
            $cloud_link = $event->getOriginal('image');
            $cloud_link = preg_replace('/(.*\/.\/*\/)\w+\//', '', $cloud_link);
            $cloud_link = str_replace("?dl=1", '', $cloud_link);
            Storage::disk('dropbox')->delete('event-images/'.$cloud_link);
          }

          $file_date_title = date('Y_m_d_H_i_s').'.jpeg';
          $full_file_address = "event-images/$file_date_title";

          $dropbox = Storage::disk('dropbox')->getDriver()->getAdapter()->getClient();
          Storage::disk('dropbox')->put($full_file_address, base64_decode($request->image));

          $shared_response = $dropbox->createSharedLinkWithSettings(
                $full_file_address,
                ["requested_visibility" => "public"]
            );
          $shared_url = str_replace("?dl=0", "?dl=1", $shared_response['url']);

          $event->image = $shared_url;
        }

        $event->save();

        return response()->json([
            'header' => 'Éxito',
            'status' => 'success',
            'messages' => ['Se ha actualizado la noticia'],
            'data' => [
                'id' => $event->id
            ]
        ]);

      } catch (Exception $e) {
        return response()->json(['header' => 'Error', 'status' => 'error', 'messages' =>
            ['Ocurrió un error actualizando noticia'],
            ['debug' => $e->getMessage() . ' on line ' . $e->getLine()]]);
      }
    }

    public function destroy(Request $request, Event $event)
    {
      try {
        Storage::disk('dropbox')->delete($event->getOriginal('image'));
        DB::table('events')->where('id', $event->id)->delete();

        return response()->json([
          'header' => 'Exito',
          'status' => 'success',
          'message' => ['Evento eliminado'],
        ]);
      } catch (\Exception $e) {
        return response()->json([
          'header' => 'Error',
          'status' => 'error',
          'messages' => ['Ocurrió un error en el registro'], ['debug' => $e->getMessage() . ' on line ' . $e->getLine()]
        ]);
      }
    }
}
