<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use DB;
use App\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(){
    return response()->json(Event::orderBy('created_at', 'desc')->get());
    }

    public function store(EventRequest $request){
      try {
        $eventInstance = Event::create([
          "title" => $request->input('title'),
          "description" => $request->input('description'),
          "created_by" => $request->input('created_by'),
        ]);

        if ($request->image['new_image']) {
          $file_date_title = date('H_i_s').'_event_image.jpeg';
          $full_file_address = "event-images/$file_date_title";
          Storage::put($full_file_address, base64_decode($request->image['new_image']['value']));

          $eventInstance->image = $full_file_address;
          $eventInstance->save();
        }

        return response()->json([
            'header' => 'Éxito',
            'status' => 'success',
            'messages' => ['Se ha registrado la noticia'],
            'data' => [
                'id' => $eventInstance->id
            ]
        ]);

      } catch (Exception $e) {
        return response()->json(['header' => 'Error', 'status' => 'error', 'messages' =>
            ['Ocurrió un error en el registro'],
            ['debug' => $e->getMessage() . ' on line ' . $e->getLine()]]);
      }
    }

    public function destroy(Request $request, Event $event)
    {
      try {
        Storage::delete($event->getOriginal('image'));
        DB::table('events')->where('id', $event->id)->delete();

        return response()->json([
          'header' => 'Exito',
          'status' => 'success',
          'message' => ['Evento eliminado'],
        ]);
      } catch (\Exception $e) {
        dd($e->getMessage());
        return response()->json([
          'header' => 'Error',
          'status' => 'error',
          'messages' => ['Ocurrió un error en el registro'], ['debug' => $e->getMessage() . ' on line ' . $e->getLine()]
        ]);
      }
    }
}
