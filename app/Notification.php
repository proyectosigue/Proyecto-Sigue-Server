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
}
