<?php

namespace App\Http\Controllers;

use App\File;
use App\Thread;
use Illuminate\Http\Request;

class FileController extends Controller
{

    public function download(File $file){
        $path = "$file->path/$file->name";
        $name = basename($file->name);
        return response()->download($path, $name);
    }

}
