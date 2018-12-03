<?php

namespace App\Http\Controllers;

use FCM;
use App\File;
use App\User;
use App\Thread;
use App\Message;
use Carbon\Carbon;
use App\Notification;
use Illuminate\Http\Request;
use App\Http\Traits\FcmStream;
use App\Http\Traits\APIResponse;
use App\Events\NewThreadMessage;
use Illuminate\Support\Facades\Event;

class MessageController extends Controller
{

    public function store(Request $request, Thread $thread, User $user)
    {
        try {

            $message = new Message();
            $message->body = $request->body;
            $message->thread()->associate($thread);
            $message->replier()->associate($user);
            $message->receiver()->associate($request->user_id_receiver);
            $message->save();

            if (isset($request->base64_file) && isset($request->file_name))
                File::upload($thread, $request->file_name, $request->base64_file, 'threads');

            $thread->updated_at = Carbon::now();
            $thread->save();

            Event::fire(new NewThreadMessage($message));

            if($message->receiver->fcm_token) {

                $threadUserNotification = $thread->whereHas('notification', function($query) use($message) {
                    $query->where('user_id', $message->receiver->id);
                })->count();

                if(!$threadUserNotification) {

                    $title = 'Mensaje de ' . $message->replier->first_name . ' sobre ' . $thread->subject;
                    FcmStream::sendMessageNotification($title, 'Tienes 1 o más mensajes', $message->receiver->fcm_token);

                    Notification::saveSingleNotification($message->receiver->id, $thread->id, $message->id);
                }
            }

            return response()->json(APIResponse::success('Mensaje enviado'));

        } catch (\Exception $e) {

            $errors = ['Ocurrió un error en el registro'.$e->getMessage() . ' on line ' . $e->getLine()." ".$e->getFile()];
            $debug_message = $e->getMessage() . ' on line ' . $e->getLine();

            return response()->json(APIResponse::error($errors, $debug_message));
        }
    }

    public function destroy(Request $request, Message $message)
    {
        try {

            $message->status = 0;
            $message->save();

            return response()->json([
                'header' => 'Éxito',
                'status' => 'success',
                'messages' => ['Mensaje desactivado'],
            ]);

        } catch (\Exception $e) {
            return response()->json(['header' => 'Error', 'status' => 'error', 'messages' =>
                ['Ocurrió un error en el registro'],
                ['debug' => $e->getMessage() . ' on line ' . $e->getLine()]]);
        }
    }

}
