<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
      "title",
      "description",
      "image",
      "created_by",
    ];

    public function created_by()
    {
      return $this->belongsTo('App\User', 'created_by');
    }

    public function getImageAttribute($value){
        return ($value) ? asset("storage")."/$value" : null;
    }

    public function getCreatedAtAttribute(){
        return Carbon::parse($this->attributes['created_at'])->format('d/m H:i:s');
    }

}
