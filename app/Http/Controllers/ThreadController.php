<?php

namespace App\Http\Controllers;

use App\Events\ThreadHistoryRequested;
use App\File;
use App\User;
use App\Thread;
use App\Message;
use Illuminate\Http\Request;
use App\Http\Traits\APIResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class ThreadController extends Controller
{

    public function show(Thread $thread, $start_message_id)
    {
        try {
            Event::fire(new ThreadHistoryRequested($thread, $start_message_id));
            return response()->json(APIResponse::success('Historial recuperado'));
        } catch (\Exception $e) {
            $errors = ['Ocurrió un error recuperando los mensajes '];
            $debug_message = $e->getMessage() . ' on line ' . $e->getLine();
            return response()->json(APIResponse::error($errors, $debug_message));
        }

    }

    // TODO Se debería de poder inyectar User $receiver_user pero lo recibe como nulo
    public function store(Request $request)
    {
        try {

            $thread = new Thread();
            $thread->subject = $request->subject;
            $thread->issuing()->associate($request->user_issuing_id);
            $thread->receiver()->associate($request->user_receiver_id);
            $thread->save();

            $api_response = APIResponse::success('Se ha creado el tema');

            $api_response['thread'] = $thread;
            return response()->json($api_response);

        } catch (\Exception $e) {

            $errors = ['Ocurrió un error'];
            $debug_message = $e->getMessage() . ' on line ' . $e->getLine() .
                ' file ' . $e->getFile();

            $api_response = APIResponse::error($errors, $debug_message);

            return response()->json($api_response);
        }

    }

    public function update(Request $request, Thread $thread)
    {
        try {

            $thread->subject = $request->subject;
            $thread->save();

        } catch (\Exception $e) {
            return response()->json(['header' => 'Error', 'status' => 'error', 'messages' =>
                ['Ocurrió un error en el registro'],
                ['debug' => $e->getMessage() . ' on line ' . $e->getLine()]]);
        }

        return response()->json([
            'header' => 'Éxito',
            'status' => 'success',
            'messages' => ['Se ha actualizado el tema'],
        ]);
    }

    public function destroy(Request $request, Thread $thread)
    {
        try {

            $thread->status = 0;
            $thread->save();

            return response()->json([
                'header' => 'Éxito',
                'status' => 'success',
                'messages' => ['Tema desactivado'],
            ]);

        } catch (\Exception $e) {
            return response()->json(['header' => 'Error', 'status' => 'error', 'messages' =>
                ['Ocurrió un error en el registro'],
                ['debug' => $e->getMessage() . ' on line ' . $e->getLine()]]);
        }
    }

    public function destroyAll(Request $request, User $user)
    {
        try {

            $user->issuingThreads()->update(['status' => 0]);
            $user->receiverThreads()->update(['status' => 0]);

            $api_response = APIResponse::success('Todos los temas han sido desactivados');
            return response()->json($api_response);

        } catch (\Exception $e) {

            $errors = ['Ocurrió un error'];
            $debug_message = $e->getMessage() . ' on line ' . $e->getLine() .
                ' file ' . $e->getFile();

            $api_response = APIResponse::error($errors, $debug_message);
            return response()->json($api_response);
        }
    }

    public function userThreads(Request $request, User $user)
    {
        $threads = Thread::active()->where(function ($q) use ($user) {
            return $q->where('user_id_issuing', $user->id)
                ->orWhere('user_id_receiver', $user->id);
        })->with(['lastMessage', 'notification'])->descendant()->get();

        return response()->json($threads);
    }

    public function threadFiles(Thread $thread){
        return response()->json($thread->files);
    }

    public function receiverThreads(Request $request, User $user)
    {
        return response()->json($user->receiverThreads()->with('messages')->get());
    }

    public function issuingThreads(Request $request, User $user)
    {
        return response()->json($user->issuingThreads()->active()->with('messages')->get());
    }

    public function threadMessages(Request $request, Thread $thread)
    {
        return response()->json($thread->with('messages')->get());
    }
}
