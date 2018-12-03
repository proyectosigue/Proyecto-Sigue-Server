<?php

namespace App;

use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'name',
        'path',
        'filable_id',
        'filable_type',
        'status',
    ];

    public function threads(){
        return $this->morphedByMany(Thread::class, 'filable');
    }

    public static function upload($entity, $file_name, $base64, $folder){
        try {

            $contents = file_get_contents($base64);

            Storage::put("$folder/$file_name", $contents);

            if($entity->files() !== null) {
                $entity->files()->save(new File([
                    'name' => $file_name,
                    'path' => "storage/$folder/"
                ]));
            }

            return response()->json([
                'header' => 'Éxito',
                'status' => 'success',
                'messages' => ['Se ha subido el archivo']
            ]);

        } catch (Exception $e) {

            return response()->json([
                'header' => 'Error',
                'status' => 'error',
                'messages' => ['Ocurrió un error, contacte al soporte'],
                ['debug' => $e->getMessage() . ' on line ' . $e->getLine()]]);

        }
    }

}
