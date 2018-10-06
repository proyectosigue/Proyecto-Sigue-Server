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

    public static function upload($entity, $file, $folder){
        try {

            $contents = file_get_contents($file);
            $f = finfo_open();
            $mime_type = finfo_buffer($f, $contents, FILEINFO_MIME_TYPE);
            $file_info = explode('/', $mime_type);
            $ext = $file_info[1];

            $file_title = date('H_i_s').".".$ext;

            Storage::put("$folder/$file_title", $contents);

            if($entity->files() !== null) {
                $entity->files()->save(new File([
                    'name' => $file_title,
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
