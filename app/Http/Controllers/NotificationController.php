<?php

namespace App\Http\Controllers;

use App\User;
use App\Thread;
use App\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
  public function store(Request $request)
  {
    $success = true;
    $count = Notification::where('thread_id', $request->thread_id)
                         ->where('user_id', $request->user_id)
                         ->count();

    if (!$count) {
        $success = Notification::insert([
          'thread_id' => $request->user_id,
          'message_id' => $request->message_id,
          'user_id' => $request->user_id,
        ]);
    }

    return response()->json([
      'status' => $success ? 'Success' : 'Failed',
      'message' => $success ? 'Notification Saved' : 'Error saving notification',
    ]);
  }

  public function destroy(Request $request)
  {
    $success = Notification::where('thread_id', $request->thread_id)
                           ->where('user_id', $request->user_id)
                           ->delete();

    return response()->json([
      'status' => $success ? 'Success' : 'Failed',
      'message' => $success ? 'Notification deleted' : 'Failed deleting notification',
    ]);
  }
}
