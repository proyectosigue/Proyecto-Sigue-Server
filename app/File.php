<?php

namespace App;

use Exception;
use Spatie\Dropbox\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'name',
        'path',
        'cloud_path',
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

            $file_name_parts = explode(".", $file_name);
            $file_name = $file_name_parts[0].'_'.date('Y_m_d_H_i_s').'.'.$file_name_parts[count($file_name_parts) - 1];

            $dropbox = Storage::disk('dropbox')->getDriver()->getAdapter()->getClient();
            Storage::disk('dropbox')->put("$folder/$file_name", $contents);

            $shared_response = $dropbox->createSharedLinkWithSettings(
                "$folder/$file_name",
                ["requested_visibility" => "public"]
            );
            $shared_url = str_replace("?dl=0", "?dl=1", $shared_response['url']);
            $shared_url = str_replace("www", "dl", $shared_url);

            if($entity->files() !== null) {
                $entity->files()->save(new File([
                    'name' => $file_name,
                    'path' => "$folder/",
                    'cloud_path' => $shared_url
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
