<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
  protected $fillable = [
    'thread_id',
    'message_id',
    'user_id',
  ];

  public static function saveSingleNotification($user_id, $thread_id, $message_id){
      $success = true;
      $count = Notification::where('thread_id', $thread_id)
          ->where('user_id',$user_id)
          ->count();

      if (!$count) {
          $success = Notification::insert([
              'thread_id' => $thread_id,
              'message_id' => $message_id,
              'user_id' => $user_id,
          ]);
      }

      return $success;
  }

}
