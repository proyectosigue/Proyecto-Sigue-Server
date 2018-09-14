<?php

namespace App\Http\Controllers;

use App\File;
use App\Thread;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index($model){
        return response()->json($model->load('files')->get());
    }

}
